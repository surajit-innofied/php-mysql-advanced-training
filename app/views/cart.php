<?php
session_start();

// Access control
if (!isset($_SESSION['user']) || (($_SESSION['user']['role'] ?? '') !== 'user')) {
    header("Location: login.php");
    exit;
}

// CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // secure token generation
}
$csrf = $_SESSION['csrf_token']; // [7]

$cart = $_SESSION['cart'] ?? [];
$total = 0.0;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Your Cart</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            margin: 0;
            padding: 20px;
        }

        .wrap {
            max-width: 900px;
            margin: 20px auto;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, .08);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 10px;
            border-bottom: 1px solid #eee;
            text-align: left;
        }

        th {
            background: #04AA6D;
            color: #fff;
        }

        .actions {
            margin-top: 15px;
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }

        .btn {
            padding: 8px 14px;
            border-radius: 6px;
            text-decoration: none;
            color: #fff;
            border: 0;
            cursor: pointer;
        }

        .btn-primary {
            background: #04AA6D;
        }

        .btn-secondary {
            background: #3498db;
        }

        .btn-danger {
            background: #e74c3c;
        }

        .inline-form {
            display: inline;
        }
    </style>
</head>

<body>
    <div class="wrap">
        <h2>Your Cart</h2>

        <?php if (empty($cart)): ?>
            <p>Your cart is empty.</p>
            <a href="../../public/index.php" class="btn btn-secondary">Continue Shopping</a>
        <?php else: ?>
            <!-- Main update form for quantities -->
            <form method="post" action="update_cart.php">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
                <input type="hidden" name="action" value="update">

                <table>
                    <tr>
                        <th style="width:34%;">Product</th>
                        <th style="width:14%;">Price</th>
                        <th style="width:20%;">Quantity</th>
                        <th style="width:20%;">Subtotal</th>
                        <th style="width:12%;">Remove</th>
                    </tr>
                    <?php foreach ($cart as $id => $item):
                        $price = (float)$item['price'];
                        $qty   = (int)$item['quantity'];
                        $name  = (string)$item['name'];
                        $subtotal = $price * $qty;
                        $total += $subtotal;
                    ?>
                        <tr>
                            <td><?= htmlspecialchars($name) ?></td>
                            <td><?= number_format($price, 2) ?></td>
                            <td>
                                <input type="number" name="quantities[<?= (int)$id ?>]"
                                    value="<?= max(0, $qty) ?>" min="0" step="1" />
                                <small style="color:#888;">(Set 0 to remove)</small>
                            </td>
                            <td><?= number_format($subtotal, 2) ?></td>
                            <td>
                                <!-- Inline remove form per item -->
                                <button class="btn btn-danger" type="submit" name="remove_id" value="<?= (int)$id ?>">Remove</button>
                            </td>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td colspan="3" style="text-align:right; font-weight:bold;">Total</td>
                        <td style="font-weight:bold;"><?= number_format($total, 2) ?></td>
                        <td></td>
                    </tr>
                </table>

                <div class="actions">
                    <a href="../../public/index.php" class="btn btn-secondary">Continue Shopping</a>
                    <button type="submit" class="btn btn-primary">Update Cart</button>
                </div>
            </form>
        <?php endif; ?>
    </div>
</body>

</html>