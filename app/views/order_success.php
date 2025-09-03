<?php
// public/order_success.php
session_start();
require_once __DIR__ . '/../../config/Db_Connect.php';

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
    <title>Order Success</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            margin: 0;
            padding: 0;
            color: #333;
        }

        .container {
            max-width: 700px;
            margin: 60px auto;
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 6px 18px rgba(0,0,0,0.1);
            text-align: center;
        }

        h2 {
            color: #04AA6D;
            margin-bottom: 10px;
        }

        h3 {
            margin-top: 30px;
            margin-bottom: 15px;
            color: #333;
        }

        p {
            font-size: 16px;
            margin: 8px 0;
        }

        ul {
            list-style: none;
            padding: 0;
            margin: 0 auto 20px;
            text-align: left;
            max-width: 500px;
        }

        ul li {
            padding: 10px;
            border-bottom: 1px solid #eee;
            font-size: 15px;
        }

        ul li:last-child {
            border-bottom: none;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #04AA6D;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            transition: 0.3s;
        }

        .btn:hover {
            background: #028a56;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Thank you! Your order has been placed</h2>
        <p><strong>Order #<?= htmlspecialchars($orderId) ?></strong></p>
        <p>Total: <strong>₹<?= number_format((float)$order['total_amount'], 2) ?></strong></p>

        <h3> Items in your order</h3>
        <ul>
            <?php foreach ($items as $it): ?>
                <li>
                    <?= htmlspecialchars($it['name']) ?>  
                    — <strong>Qty <?= (int)$it['quantity'] ?></strong>  
                    — ₹<?= number_format((float)$it['unit_price'], 2) ?>
                </li>
            <?php endforeach; ?>
        </ul>

        <a href="../../public/index.php" class="btn">⬅ Continue Shopping</a>
    </div>
</body>
</html>

