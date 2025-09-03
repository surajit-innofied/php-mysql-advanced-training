<?php
// public/orders.php
session_start();
require_once __DIR__ . '/../../config/Db_Connect.php';

if (!isset($_SESSION['user']) || empty($_SESSION['user']['id'])) {
    header('Location: login.php');
    exit;
}

$userId = (int)$_SESSION['user']['id'];

// Fetch all orders for the logged-in user + address info
$stmt = $pdo->prepare("
    SELECT o.*, a.address AS address, a.city, a.state, a.zip, a.country
    FROM orders o
    LEFT JOIN addresses a ON o.address_id = a.id
    WHERE o.user_id = ?
    ORDER BY o.created_at DESC
");
$stmt->execute([$userId]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// For each order, fetch items
$orderItems = [];
if ($orders) {
    $orderIds = array_column($orders, 'id');
    $in  = str_repeat('?,', count($orderIds) - 1) . '?';
    $stmt = $pdo->prepare("
        SELECT oi.order_id, oi.quantity, oi.unit_price,
               p.name
        FROM order_items oi
        JOIN new_products p ON oi.product_id = p.id
        WHERE oi.order_id IN ($in)
        ORDER BY oi.order_id DESC
    ");
    $stmt->execute($orderIds);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($rows as $r) {
        $orderItems[$r['order_id']][] = $r;
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>My Orders</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            padding: 20px;
        }

        .wrap {
            max-width: 1000px;
            margin: 20px auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, .06);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }

        th,
        td {
            padding: 12px;
            border-bottom: 1px solid #eee;
            text-align: left;
        }

        th {
            background: #04AA6D;
            color: #fff;
        }

        .btn {
            padding: 8px 16px;
            border-radius: 6px;
            background: #04AA6D;
            color: #fff;
            text-decoration: none;
            border: 0;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn:hover {
            background: #028a56;
        }

        h3 {
            margin-top: 30px;
            color: #2c3e50;
            background: #ecf0f1;
            padding: 10px 15px;
            border-left: 5px solid #04AA6D;
            border-radius: 4px;
        }

        .address-box {
            background: #fafafa;
            padding: 10px;
            border: 1px solid #eee;
            border-radius: 5px;
            margin-bottom: 15px;
            font-size: 14px;
            color: #444;
        }
    </style>
</head>

<body>
    <div class="wrap">
        <div style="display:flex; justify-content:space-between; align-items:center;">
            <h2 style="color:#04AA6D; margin:0;">My Orders</h2>
            <a href="../../public/index.php" class="btn">⬅ Continue Shopping</a>
        </div>

        <?php $count = 1; ?>

        <?php if (empty($orders)): ?>
            <p>No orders found. <a href="../../public/index.php">Shop Now</a></p>
        <?php else: ?>
            <?php foreach ($orders as $order): ?>
                <h3>
                    Order #<?= (int)$count ?> — Total: <?= number_format((float)$order['total_amount'], 2) ?>
                    <small>(Placed: <?= htmlspecialchars($order['created_at']) ?>)</small>
                </h3>

                <div class="address-box">
                    <strong>Delivery Address:</strong><br>
                    <?php if (!empty($order['address'])): ?>
                        <?= htmlspecialchars($order['address']) ?>,
                        <?= htmlspecialchars($order['city']) ?>,
                        <?= htmlspecialchars($order['state']) ?> -
                        <?= htmlspecialchars($order['zip']) ?>,
                        <?= htmlspecialchars($order['country']) ?>
                    <?php else: ?>
                        <em>No address given by user</em>
                    <?php endif; ?>
                </div>

                <table>
                    <tr>
                        <th>Product</th>
                        <th>Unit Price</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                    </tr>
                    <?php foreach ($orderItems[$order['id']] ?? [] as $item): ?>
                        <tr>
                            <td><?= htmlspecialchars($item['name']) ?></td>
                            <td><?= number_format((float)$item['unit_price'], 2) ?></td>
                            <td><?= (int)$item['quantity'] ?></td>
                            <td><?= number_format((float)$item['unit_price'] * (int)$item['quantity'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
                <?php $count++; ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>

</html>
