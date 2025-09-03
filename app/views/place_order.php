<?php
session_start();
require_once __DIR__ . '/../../config/Db_Connect.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$userId = (int)$_SESSION['user']['id'];

// ---- Validate POST ----
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Invalid request");
}

$address  = trim($_POST['address'] ?? '');
$city     = trim($_POST['city'] ?? '');
$state    = trim($_POST['state'] ?? '');
$zip      = trim($_POST['zip'] ?? '');
$country  = trim($_POST['country'] ?? '');

if ($address === '' || $city === '' || $state === '' || $zip === '' || $country === '') {
    $_SESSION['flash_error'] = "Please fill in all required address fields.";
    header("Location: address.php");
    exit;
}

// ---- Fetch cart items ----
$stmt = $pdo->prepare("
    SELECT c.id as cart_id, c.quantity,
           p.id as product_id, p.name, p.price, p.stock
    FROM cart c
    JOIN new_products p ON c.product_id = p.id
    WHERE c.user_id = ?
");
$stmt->execute([$userId]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($items)) {
    die("Cart is empty.");
}

// ---- Calculate total ----
$total = 0;
foreach ($items as $it) {
    $total += $it['price'] * $it['quantity'];
}

try {
    $pdo->beginTransaction();

    // Insert address
    $stmt = $pdo->prepare("
        INSERT INTO addresses (user_id, address, city, state, zip, country) 
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $userId,
        $address,
        $city,
        $state,
        $zip,
        $country
    ]);
    $addressId = $pdo->lastInsertId();

    // Insert order
    $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_amount, address_id) VALUES (?, ?, ?)");
    $stmt->execute([$userId, $total, $addressId]);
    $orderId = $pdo->lastInsertId();

    // Insert order items + reduce stock
    $stmtItem = $pdo->prepare("
        INSERT INTO order_items (order_id, product_id, quantity, unit_price)
        VALUES (?, ?, ?, ?)
    ");
    $stmtStock = $pdo->prepare("UPDATE new_products SET stock = stock - ? WHERE id = ?");

    foreach ($items as $it) {
        if ($it['quantity'] > $it['stock']) {
            throw new Exception("Not enough stock for product: " . $it['name']);
        }
        $stmtItem->execute([$orderId, $it['product_id'], $it['quantity'], $it['price']]);
        $stmtStock->execute([$it['quantity'], $it['product_id']]);
    }

    // Clear cart
    $pdo->prepare("DELETE FROM cart WHERE user_id = ?")->execute([$userId]);

    $pdo->commit();

    // Redirect to success page
    
     header("Location: order_success.php?id=" . $orderId);

    exit();
} catch (Exception $e) {
    $pdo->rollBack();
    die("Order failed: " . $e->getMessage());
}
