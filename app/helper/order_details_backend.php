<?php
// public/order_details_backend.php
// This file contains the backend logic to fetch and validate order data.
// It is intended to be included by the frontend view file.

session_start();
require_once __DIR__ . '/../../config/Db_Connect.php';

// Ensure the user is authenticated.
if (!isset($_SESSION['user']) || empty($_SESSION['user']['id'])) {
    header('Location: /../../../public/login.php');
    exit;
}

$orderId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($orderId <= 0) {
    header('Location: /../../../public/index.php');
    exit;
}

// Fetch order details and items, ensuring they belong to the current user.
$userId = (int)$_SESSION['user']['id'];
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
$stmt->execute([$orderId, $userId]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    // Redirect if the order does not exist or does not belong to the user.
    header('Location: /../../../public/index.php');
    exit;
}

// Fetch the items associated with the order.
$stmt = $pdo->prepare("
    SELECT oi.*, p.name 
    FROM order_items oi
    JOIN new_products p ON oi.product_id = p.id
    WHERE oi.order_id = ?
");
$stmt->execute([$orderId]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// The variables $order and $items are now ready to be used by the frontend.
?>
