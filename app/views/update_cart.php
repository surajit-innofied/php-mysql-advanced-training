<?php
session_start();
require_once __DIR__ . '/../../config/Db_Connect.php';

if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'user') {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['quantities']) && is_array($_POST['quantities'])) {
    foreach ($_POST['quantities'] as $id => $qty) {
        $id = (int)$id;
        $qty = (int)$qty;

        if (!isset($_SESSION['cart'][$id])) {
            continue;
        }

        // Fetch latest stock
        $stmt = $pdo->prepare("SELECT stock FROM new_products WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            // product gone: remove it from cart
            unset($_SESSION['cart'][$id]);
            continue;
        }

        $stock = (int)$row['stock'];

        //remove item
        if ($qty <= 0) {
            unset($_SESSION['cart'][$id]);           // remove item
        } elseif ($stock <= 0) {
            unset($_SESSION['cart'][$id]);           // out of stock now
        } else {
            $_SESSION['cart'][$id]['quantity'] = min($qty, $stock);  // cap by stock
        }

        if (isset($_POST['remove_id'])) {
            $pid = (int)$_POST['remove_id'];
            if ($pid > 0 && isset($_SESSION['cart'][$pid])) {
                unset($_SESSION['cart'][$pid]);
            }
            header('Location: cart.php');
            exit;
        }
    }
}

header("Location: cart.php");
exit;
