<?php
session_start();
require_once __DIR__ . '/../../middleware/auth.php';
require_once __DIR__ . '/../../helper/admin_dashboard_helper.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .card {
            border: 1px solid #ddd; padding: 20px; border-radius: 8px;
            width: 300px; margin-bottom: 15px; box-shadow: 0px 2px 5px rgba(0,0,0,0.1);
        }
        h1 { color: #333; }
    </style>
</head>
<body>
    <h1>Welcome Admin</h1>
    <div class="card">
        <p><strong>Name:</strong> <?= htmlspecialchars($adminName) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($adminEmail) ?></p>
        <p><strong>Total Products Created:</strong> <?= $totalProducts ?></p>
    </div>
    <p><a href="/../../../public/index.php">Back to Home Page</a></p>
</body>
</html>