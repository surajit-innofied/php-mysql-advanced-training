<?php
// public/checkout_logic.php
// This file contains the backend logic for the checkout page.
// It is intended to be included by the frontend view file.

session_start();
require_once __DIR__ . '/../../config/Db_Connect.php';

// Ensure the user is authenticated.
if (!isset($_SESSION['user']) || empty($_SESSION['user']['id'])) {
    header('Location: login.php');
    exit;
}
$userId = (int)($_SESSION['user']['id'] ?? $_SESSION['user_id'] ?? 0);

$errorMsg = "";

// Fetch items for display and validation
$stmt = $pdo->prepare("
    SELECT c.id AS cart_id, c.product_id, c.quantity, p.name, p.price, p.stock
    FROM cart c
    JOIN new_products p ON c.product_id = p.id
    WHERE c.user_id = ?
    ORDER BY c.created_at DESC
");
$stmt->execute([$userId]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($items)) {
    $_SESSION['flash_error'] = 'No items to checkout.';
    header('Location: views/cart/view_cart.php'); // Changed from view_cart.php
    exit;
}

// If the form is submitted, validate stock before proceeding
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($items as $it) {
        if ($it['quantity'] > $it['stock']) {
            $errorMsg = "Product <b>" . htmlspecialchars($it['name']) . "</b> has only {$it['stock']} in stock. Please update your cart.";
            break;
        }
    }

    if (empty($errorMsg)) {
        // Stock is valid, proceed to the next step
        header("Location: /../../app/views/payment/address.php");
        exit;
    }
}

// Calculate the total price for display
$total = 0.0;
foreach ($items as $it) {
    $total += ((float)$it['price']) * ((int)$it['quantity']);
}
?>
