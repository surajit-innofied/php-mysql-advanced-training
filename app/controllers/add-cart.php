
<?php
require_once __DIR__ . '/../../config/Db_Connect.php';
// public/add_to_cart.php
session_start();


// ensure user logged in and is normal user
if (!isset($_SESSION['user']) || empty($_SESSION['user']['id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['product_id'])) {
    header('Location: index.php');
    exit;
}

$userId = (int)$_SESSION['user']['id'];
$productId = (int)$_POST['product_id'];
$quantity = isset($_POST['quantity']) ? max(1, (int)$_POST['quantity']) : 1;

// optional: check product exists (recommended)
$stmt = $pdo->prepare("SELECT id, stock FROM new_products WHERE id = ?");
$stmt->execute([$productId]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$product) {
    $_SESSION['flash_error'] = 'Product not found.';
    header('Location: index.php');
    exit;
}

// Simple approach: increment if exists, else insert
$stmt = $pdo->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?");
$stmt->execute([$userId, $productId]);
$existing = $stmt->fetch(PDO::FETCH_ASSOC);

if ($existing) {
    $newQty = $existing['quantity'] + $quantity;
    $stmt = $pdo->prepare("UPDATE cart SET quantity = ?, updated_at = NOW() WHERE id = ?");
    $stmt->execute([$newQty, $existing['id']]);
} else {
    $stmt = $pdo->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
    $stmt->execute([$userId, $productId, $quantity]);
}

// redirect to view cart
header('Location:  /../app/views/cart/view_cart.php');
exit;
