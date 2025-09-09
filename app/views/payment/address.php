<?php
// public/address.php
require_once __DIR__ . '/../../middleware/user.php';
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['checkout'] = [
        'address' => $_POST['address'],
        'city'    => $_POST['city'],
        'state'   => $_POST['state'],
        'zip'     => $_POST['zip'],
        'country' => $_POST['country'],
    ];
    header("Location: payment.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Checkout - Address</title>
    <link rel="stylesheet" href="/../../../public/css/address.css">
</head>
<body>
<div class="wrap">
    <h2>Enter Shipping Address</h2>
    <form method="post">
        <input type="text" name="address" placeholder="Address" required>
        <input type="text" name="city" placeholder="City" required>
        <input type="text" name="state" placeholder="State" required>
        <input type="text" name="zip" placeholder="ZIP Code" required>
        <input type="text" name="country" placeholder="Country" required>
        <button type="submit">Next → Payment</button>
    </form>
    <a href="checkout.php" class="back-link">← Back to Checkout</a>
</div>
</body>
</html>
