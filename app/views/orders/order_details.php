<?php
// public/order_success.php (acts as order_details now)
session_start();
require_once __DIR__ . '/../../../config/Db_Connect.php';
require_once __DIR__ . '/../../middleware/user.php';

if (!isset($_SESSION['user']) || empty($_SESSION['user']['id'])) {
    header('Location: login.php');
    exit;
}

$orderId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($orderId <= 0) {
    header('Location: ../../public/index.php');
    exit;
}

// fetch order + items (ensure belongs to user)
$userId = (int)$_SESSION['user']['id'];
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
$stmt->execute([$orderId, $userId]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$order) {
    header('Location: ../../public/index.php');
    exit;
}

$stmt = $pdo->prepare("
    SELECT oi.*, p.name 
    FROM order_items oi
    JOIN new_products p ON oi.product_id = p.id
    WHERE oi.order_id = ?
");
$stmt->execute([$orderId]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Order Details</title>
    <link rel="stylesheet" href="/../../../public/css/order_details.css">
</head>
<body>
    <div class="container">
        <h2>Here is your order details</h2>
        <p><strong>Order #<?= htmlspecialchars($orderId) ?></strong></p>
        <p>Status: <strong><?= htmlspecialchars(ucfirst($order['statuss'])) ?></strong></p>
        <p>Total: <strong>₹<?= number_format((float)$order['total_amount'], 2) ?></strong></p>

        <h3>Items in your order</h3>
        <ul>
            <?php foreach ($items as $it): ?>
                <li>
                    <?= htmlspecialchars($it['name']) ?>  
                    — <strong>Qty <?= (int)$it['quantity'] ?></strong>  
                    — ₹<?= number_format((float)$it['unit_price'], 2) ?>
                </li>
            <?php endforeach; ?>
        </ul>

        <div class="btn-wrap">
            <a href="orders.php" class="btn">View All Orders</a>
            <a href="/../../../public/index.php" class="btn">Continue Shopping</a>
        </div>
    </div>
</body>
</html>
