<?php
// public/payment.php
session_start();
require_once __DIR__ . '/../../config/Db_Connect.php';
require __DIR__ . '/../../vendor/autoload.php'; // Stripe SDK

// ✅ Load .env file
$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__, 2));

$dotenv->load();
// ✅ Get secret key from env
$stripeSecret = $_ENV['STRIPE_SECRET_KEY'] ?? null;

if (!$stripeSecret) {
    die("Stripe secret key not found. Please set STRIPE_SECRET_KEY in your .env file.");
}
// ✅ Set Stripe API key
\Stripe\Stripe::setApiKey($stripeSecret);



// STEP 1: Handle redirect BACK from Stripe (success or failure)
if (isset($_GET['status']) && isset($_GET['session_id'])) {
  $status    = $_GET['status'];
  $sessionId = $_GET['session_id'];
  $userId    = $_SESSION['user']['id'];

  try {
    $session = \Stripe\Checkout\Session::retrieve($sessionId);
    $paymentStatus = $session->payment_status === "paid" ? "paid" : "failed";

    // Fetch cart
    $stmt = $pdo->prepare("
            SELECT c.quantity, p.id as product_id, p.price
            FROM cart c
            JOIN new_products p ON c.product_id = p.id
            WHERE c.user_id = ?
        ");
    $stmt->execute([$userId]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($items) {
      $total = 0;
      foreach ($items as $it) {
        $total += $it['price'] * $it['quantity'];
      }

      // ✅ Get address details from session
      $addressData = $_SESSION['checkout'] ?? null;

      if ($addressData) {
        // Save address in `addresses` table
        $stmt = $pdo->prepare("
    INSERT INTO addresses (user_id, address, city, state, zip, country, created_at)
    VALUES (?, ?, ?, ?, ?, ?, NOW())
");
        $stmt->execute([
          $userId,
          $addressData['address'],
          $addressData['city'],
          $addressData['state'],
          $addressData['zip'],
          $addressData['country']
        ]);
        $addressId = $pdo->lastInsertId();
      } else {
        $addressId = null; // fallback
      }

      $pdo->beginTransaction();

      // ✅ Save order with address_id
      $stmt = $pdo->prepare("
    INSERT INTO orders (user_id, address_id, total_amount, statuss, payment_id, created_at)
    VALUES (?, ?, ?, ?, ?, NOW())
");
      $stmt->execute([
        $userId,
        $addressId,
        $total,
        $paymentStatus,
        $sessionId   // Stripe session ID as payment reference
      ]);
      $orderId = $pdo->lastInsertId();

      // Save items
      foreach ($items as $it) {
        $stmt = $pdo->prepare("
                    INSERT INTO order_items (order_id, product_id, quantity, unit_price)
                    VALUES (?, ?, ?, ?)
                ");
        $stmt->execute([$orderId, $it['product_id'], $it['quantity'], $it['price']]);

        // ✅ Reduce stock ONLY if payment successful
        if ($paymentStatus === "paid") {
          $stmt = $pdo->prepare("
                        UPDATE new_products
                        SET stock = stock - ?
                        WHERE id = ? AND stock >= ?
                    ");
          $stmt->execute([$it['quantity'], $it['product_id'], $it['quantity']]);
        }
      }



      // ✅ Clear cart only if payment successful
      if ($paymentStatus === "paid") {
        $pdo->prepare("DELETE FROM cart WHERE user_id = ?")->execute([$userId]);
      }

      $pdo->commit();
    }


    // 🔥 Redirect to order details page (instead of orders list)
    header("Location: http://localhost:8000/app/views/order_details.php?id=" . $orderId);

    exit;
  } catch (Exception $e) {
    die("Payment handling failed: " . $e->getMessage());
  }
}

// STEP 2: Normal entry → create Stripe Checkout session
if (!isset($_SESSION['user']) || !isset($_SESSION['checkout'])) {
  header("Location: address.php");
  exit;
}

$userId = $_SESSION['user']['id'];

// Fetch cart items
$stmt = $pdo->prepare("
    SELECT c.quantity, p.id as product_id, p.name, p.price
    FROM cart c
    JOIN new_products p ON c.product_id = p.id
    WHERE c.user_id = ?
");
$stmt->execute([$userId]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$items) {
  die("Cart is empty.");
}

$line_items = [];
foreach ($items as $it) {
  $line_items[] = [
    'price_data' => [
      'currency' => 'usd',
      'product_data' => ['name' => $it['name']],
      'unit_amount' => $it['price'] * 100,
    ],
    'quantity' => $it['quantity'],
  ];
}

$checkout_session = \Stripe\Checkout\Session::create([
  'payment_method_types' => ['card'],
  'line_items'           => $line_items,
  'mode'                 => 'payment',
  'success_url' => "http://localhost:8000/app/views/payment.php?status=success&session_id={CHECKOUT_SESSION_ID}",
  'cancel_url'  => "http://localhost:8000/app/views/payment.php?status=failure&session_id={CHECKOUT_SESSION_ID}",
]);

header("Location: " . $checkout_session->url);
exit;
