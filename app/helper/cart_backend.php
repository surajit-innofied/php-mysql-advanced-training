<?php
// public/my_cart_data.php

// This file contains the backend logic to fetch cart data.
// It is included by the frontend file and does not output any HTML.

// Ensure the user is authenticated.
if (!isset($_SESSION['user']) || empty($_SESSION['user']['id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../../config/Db_Connect.php';

$userId = (int)$_SESSION['user']['id'];

// Fetch only existing cart rows
$stmt = $pdo->prepare("
    SELECT c.id as cart_id, c.product_id, c.quantity,
           p.name, p.price, p.stock, p.file_link
    FROM cart c
    JOIN new_products p ON c.product_id = p.id
    WHERE c.user_id = ?
    ORDER BY c.created_at DESC
");
$stmt->execute([$userId]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total = 0.0;
foreach ($items as $it) {
    $total += ((float)$it['price']) * ((int)$it['quantity']);
}

// The variables $items and $total are now ready to be used by the frontend.
?>
