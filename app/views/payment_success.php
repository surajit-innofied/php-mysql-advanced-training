<?php
session_start();
require_once __DIR__ . '/../../config/Db_Connect.php';

$orderId = (int)$_GET['order_id'];

// ✅ Update order status to "paid"
$stmt = $pdo->prepare("UPDATE orders SET status = 'paid' WHERE id = ?");
$stmt->execute([$orderId]);

// ✅ Reduce stock
$stmt = $pdo->prepare("
    SELECT product_id, quantity FROM order_items WHERE order_id = ?
");
$stmt->execute([$orderId]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($items as $item) {
    $stmt = $pdo->prepare("UPDATE new_products SET stock = stock - ? WHERE id = ?");
    $stmt->execute([$item['quantity'], $item['product_id']]);
}

// ✅ Redirect to order success page
header("Location: order_success.php?id=$orderId");
exit;
