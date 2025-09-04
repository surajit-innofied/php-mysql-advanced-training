<?php
// public/checkout.php
session_start();
require_once __DIR__ . '/../../config/Db_Connect.php';

// Ensure logged in
if (!isset($_SESSION['user']) || empty($_SESSION['user']['id'])) {
    header('Location: login.php');
    exit;
}
$userId = (int)($_SESSION['user']['id'] ?? $_SESSION['user_id'] ?? 0);

$errorMsg = "";

// If form submitted (Next Step clicked)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Fetch items again
    $stmt = $pdo->prepare("
        SELECT c.id AS cart_id, c.product_id, c.quantity, p.name, p.price, p.stock
        FROM cart c
        JOIN new_products p ON c.product_id = p.id
        WHERE c.user_id = ?
    ");
    $stmt->execute([$userId]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($items as $it) {
        if ($it['quantity'] > $it['stock']) {
            $errorMsg = "Product <b>" . htmlspecialchars($it['name']) . "</b> has only {$it['stock']} in stock. Please update your cart.";
            break;
        }
    }

    if (!$errorMsg) {
        // ✅ Stock valid → move to address page
        header("Location: address.php");
        exit;
    }
}

// Fetch items (for display)
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
    header('Location: view_cart.php');
    exit;
}

$total = 0.0;
foreach ($items as $it) {
    $total += ((float)$it['price']) * ((int)$it['quantity']);
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Checkout</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f6f9; padding: 20px; }
        .wrap { max-width: 900px; margin: 20px auto; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,.06); }
        table { width: 100%; border-collapse: collapse; }
        th,td { padding: 12px; border-bottom: 1px solid #eee; text-align: left; }
        th { background: #04AA6D; color: #fff; }
        .btn { padding: 8px 12px; border-radius: 6px; background: #04AA6D; color: #fff; text-decoration: none; border: 0; cursor: pointer; }
        .error { background:#f8d7da; color:#721c24; padding:10px; border-radius:5px; margin-bottom:15px; }
    </style>
</head>
<body>
<div class="wrap">
    <h2>Checkout</h2>

    <?php if ($errorMsg): ?>
        <div class="error"><?= $errorMsg ?></div>
    <?php endif; ?>

    <table>
        <tr>
            <th>Product</th>
            <th>Price</th>
            <th>Qty</th>
            <th>Subtotal</th>
            <th>Available</th>
        </tr>
        <?php foreach ($items as $it): ?>
            <tr>
                <td><?= htmlspecialchars($it['name']) ?></td>
                <td><?= number_format((float)$it['price'], 2) ?></td>
                <td><?= (int)$it['quantity'] ?></td>
                <td><?= number_format(((float)$it['price'] * (int)$it['quantity']), 2) ?></td>
                <td><?= ((int)$it['stock'] > 0) ? (int)$it['stock'] : 'Out of stock' ?></td>
            </tr>
        <?php endforeach; ?>
        <tr>
            <td colspan="3" style="text-align:right;font-weight:bold">Total</td>
            <td style="font-weight:bold"><?= number_format($total, 2) ?></td>
            <td></td>
        </tr>
    </table>

    <!-- Validate stock on button click -->
    <form method="post" action="checkout.php">
        <p style="margin-top:15px;">
            <button type="submit" class="btn">Next Step</button>
            <a href="view_cart.php" style="margin-left:10px;">Back to Cart</a>
        </p>
    </form>
</div>
</body>
</html>
