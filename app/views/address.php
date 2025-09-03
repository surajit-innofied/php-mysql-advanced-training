<?php
session_start();
if (!isset($_SESSION['user'])|| $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Shipping Address</title>
</head>

<body>
    <h2>Enter Shipping Address</h2>
    <form action="place_order.php" method="post">

        <label for="address">Address:
            <input type="text" name="address" placeholder="Full Address" required>
        </label>

        <label>City:
            <input type="text" name="city" required>
        </label><br>

        <label>State:
            <input type="text" name="state" required>
        </label><br>

        <label>Zip:
            <input type="text" name="zip" required>
        </label><br>

        <label>Country:
            <input type="text" name="country" required>
        </label><br>

        <button type="submit">Place Order</button>
    </form>

</body>

</html>