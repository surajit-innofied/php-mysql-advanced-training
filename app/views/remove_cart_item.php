<?php
// public/remove_cart_item.php
session_start();
require_once __DIR__ . '/../../config/Db_Connect.php';

if (!isset($_SESSION['user']) || empty($_SESSION['user']['id'])) {
    header('Location: login.php');
    exit;
}

$userId = (int)$_SESSION['user']['id'];
$cartId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($cartId > 0) {
    $stmt = $pdo->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
    $stmt->execute([$cartId, $userId]);
}

header('Location:  /../app/views/view_cart.php');
exit;
