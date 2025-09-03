<?php
// public/view_cart.php
session_start();
require_once __DIR__ . '/../../config/Db_Connect.php';

if (!isset($_SESSION['user']) || empty($_SESSION['user']['id'])) {
    header('Location: login.php');
    exit;
}

$userId = (int)$_SESSION['user']['id'];

// fetch cart rows joined to products
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
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>My Cart</title>
    <style>
        /* minimal styling — adjust to match your app */
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            padding: 20px;
        }

        .wrap {
            max-width: 1000px;
            margin: 20px auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, .06);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 12px;
            border-bottom: 1px solid #eee;
            text-align: left;
        }

        th {
            background: #04AA6D;
            color: #fff;
        }

        .actions {
            display: flex;
            gap: 8px;
        }

        .btn {
            padding: 8px 12px;
            border-radius: 6px;
            background: #04AA6D;
            color: #fff;
            text-decoration: none;
            border: 0;
            cursor: pointer;
        }

        .btn.danger {
            background: #e74c3c;
        }

        input.qty {
            width: 70px;
            padding: 6px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
    </style>
</head>

<body>
    <div class="wrap">
        <h2>Your Cart</h2>

        <?php if (empty($items)): ?>
            <p>Your cart is empty. <a href="../../public/index.php">Continue shopping</a></p>
        <?php else: ?>
            <form method="post" action="update_cart.php">
                <table>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Qty</th>
                        <th>Subtotal</th>
                        <th>Action</th>
                        <!-- <th>Order</th> -->
                    </tr>
                    <?php foreach ($items as $it):
                        $subtotal = (float)$it['price'] * (int)$it['quantity'];
                    ?>
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
                                <a class="btn danger" href="remove_cart_item.php?id=<?= (int)$it['cart_id'] ?>">Remove</a>
                            </td>
                            <!-- <td>
                                <form action="checkout.php" method="get" style="display:inline">
                                    <input type="hidden" name="cart_item_id" value="<?= (int)$cart_id ?>">
                                    <button type="submit" class="btn">Buy Now</button>
                                </form>
                            </td> -->

                        </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td colspan="3" style="text-align:right;font-weight:bold">Total</td>
                        <td style="font-weight:bold"><?= number_format($total, 2) ?></td>
                        <td></td>
                    </tr>
                </table>

                <div style="margin-top:12px; display:flex; gap:8px; justify-content:flex-end;">
                    <a class="btn" href="../../public/index.php">Continue Shopping</a>
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