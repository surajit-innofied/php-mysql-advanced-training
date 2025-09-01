<?php
session_start();
require_once __DIR__ . '/../../config/Db_Connect.php';

// Ensure only admin can access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Fetch admin info
$adminName = $_SESSION['user_name'];
$adminEmail = $_SESSION['user_email'];
$adminId = $_SESSION['user_id'];

// Count products created by this admin
$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM new_products WHERE created_by = ?");
$stmt->execute([$adminId]);
$totalProducts = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
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
        <?php echo $_SESSION['name']?>
        <p><strong>Name:</strong> <?= $_SESSION['user_name'] ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($adminEmail) ?></p>
        <p><strong>Total Products Created:</strong> <?= $totalProducts ?></p>
    </div>
    <p><a href="../../public/index.php">Back to Home Page</a></p>
</body>
</html>
