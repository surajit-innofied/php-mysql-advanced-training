


<?php
// public/update_cart.php
session_start();
require_once __DIR__ . '/../../config/Db_Connect.php';

if (!isset($_SESSION['user']) || empty($_SESSION['user']['id'])) {
    header('Location: login.php');
    exit;
}

$userId = (int)$_SESSION['user']['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['quantities']) && is_array($_POST['quantities'])) {
    foreach ($_POST['quantities'] as $cartId => $qty) {
        $cartId = (int)$cartId;
        $qty = (int)$qty;

        // ensure the cart row belongs to this user
        $stmt = $pdo->prepare("SELECT product_id FROM cart WHERE id = ? AND user_id = ?");
        $stmt->execute([$cartId, $userId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) continue;

        if ($qty <= 0) {
            // remove
            $stmt = $pdo->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
            $stmt->execute([$cartId, $userId]);
        } else {
            // update
            $stmt = $pdo->prepare("UPDATE cart SET quantity = ?, updated_at = NOW() WHERE id = ? AND user_id = ?");
            $stmt->execute([$qty, $cartId, $userId]);
        }
    }
}

header('Location:  /../app/views/view_cart.php');
exit;
