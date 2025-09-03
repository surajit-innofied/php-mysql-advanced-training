<?php
session_start();
require_once __DIR__ . '/../../config/Db_Connect.php';
require '../../vendor/autoload.php'; // Stripe SDK

\Stripe\Stripe::setApiKey(""); // your secret key



if (!isset($_SESSION['pending_order_id'])) {
  echo 'issue';
    header("Location: ../../public/index.php");
    exit;
}

$orderId = $_SESSION['pending_order_id'];

// Fetch order details
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->execute([$orderId]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    die("Order not found");
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Payment</title>
  <script src="https://js.stripe.com/v3/"></script>
</head>
<body>
  <h2>Pay for Order #<?= $orderId ?></h2>
  <p>Total Amount: ₹<?= number_format($order['total_amount'],2) ?></p>

  <button id="payBtn">Pay with Stripe</button>

  <script>
    const stripe = Stripe("ppk_test_51S3EVwFUS8k2RZWRzMqvgmOCxgKratnfT7Sh44gPda5UyYV8gXtQpZdadbpytwB9dWdnQ5LTEOQh9HxAWHNK3Itv003K7ltKUm"); // your publishable key

    document.getElementById("payBtn").addEventListener("click", function() {
        fetch("process_payment.php?order_id=<?= $orderId ?>")
          .then(res => res.json())
          .then(data => {
              return stripe.redirectToCheckout({ sessionId: data.id });
          })
          .then(result => {
              if (result.error) {
                  alert(result.error.message);
              }
          });
    });
  </script>
</body>
</html>
