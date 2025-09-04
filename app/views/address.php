<?php
// public/address.php
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
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            padding: 20px;
        }
        .wrap {
            max-width: 600px;
            margin: 40px auto;
            background: #fff;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, .08);
        }
        h2 {
            margin-bottom: 20px;
            color: #333;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        input[type="text"] {
            margin-bottom: 15px;
            padding: 12px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 6px;
            outline: none;
            transition: border-color 0.2s;
        }
        input[type="text"]:focus {
            border-color: #04AA6D;
        }
        button {
            background: #04AA6D;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 6px;
            font-size: 15px;
            cursor: pointer;
            transition: background 0.2s;
        }
        button:hover {
            background: #038a59;
        }
        .back-link {
            display: inline-block;
            margin-top: 15px;
            color: #04AA6D;
            text-decoration: none;
        }
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
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
