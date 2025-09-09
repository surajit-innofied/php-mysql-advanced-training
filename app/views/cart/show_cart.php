<?php
session_start();
// Include the backend logic file to populate the $items and $total variables.
require_once __DIR__ . '/../../helper/cart_backend.php';
require_once __DIR__ . '/../../middleware/user.php';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>My Cart</title>
    <link rel="stylesheet" href="/../../../public/css/show_cart.css">
</head>
<body>
    <div class="wrap">
        <h2>Your Cart</h2>

        <?php if (empty($items)): ?>
            <p>Your cart is empty. <a href="http://localhost:8000/public/index.php">Continue shopping</a></p>
        <?php else: ?>
            <form method="post" action="/../../app/controllers/update_cart.php">
                <table>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Qty</th>
                        <th>Subtotal</th>
                        <th>Action</th>
                    </tr>
                    <?php foreach ($items as $it): ?>
                        <?php $subtotal = (float)$it['price'] * (int)$it['quantity']; ?>
                        <tr>
                            <td><?= htmlspecialchars($it['name']) ?></td>
                            <td><?= number_format((float)$it['price'], 2) ?></td>
                            <td>
                                <input class="qty" type="number" name="quantities[<?= (int)$it['cart_id'] ?>]"
                                    value="<?= (int)$it['quantity'] ?>" min="0">
                                <br><small style="color:#666">Stock: <?= (int)$it['stock'] ?></small>
                            </td>
                            <td><?= number_format($subtotal, 2) ?></td>
                            <td>
                                <a class="btn danger" href="/../../app/controllers/remove_cart_item.php?id=<?= (int)$it['cart_id'] ?>">Remove</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td colspan="3" style="text-align:right;font-weight:bold">Total</td>
                        <td style="font-weight:bold"><?= number_format($total, 2) ?></td>
                        <td></td>
                    </tr>
                </table>

                <div style="margin-top:12px; display:flex; gap:8px; justify-content:flex-end;">
                    <a class="btn" href="http://localhost:8000/public/index.php">Continue Shopping</a>
                    <button type="submit" class="btn">Update Cart</button>
                </div>
            </form>

            <form action="checkout.php" method="get">
                <button type="submit" class="btn">Proceed to Checkout (Buy All)</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
