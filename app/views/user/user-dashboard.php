<?php
// app/views/user_dashboard.php
session_start();
require_once __DIR__ . '/../../middleware/user.php';
// normalize session fields (support both styles you've used)
$role = $_SESSION['role'] ?? ($_SESSION['user']['role'] ?? null);
$user_name = $_SESSION['user_name'] ?? ($_SESSION['user']['name'] ?? '');
$user_email = $_SESSION['user_email'] ?? ($_SESSION['user']['email'] ?? '');

if ($role !== 'user') {
    // redirect to admin dashboard if not a normal user
    header("Location: /../admin/admin-dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>User Dashboard</title>
    <style>
        body{ font-family: Arial, sans-serif; background:#f4f6f9; margin:0; padding:20px; }
        .dashboard-container{ max-width:600px; margin:50px auto; background:#fff; padding:30px; border-radius:12px; box-shadow:0 6px 16px rgba(0,0,0,.08); text-align:center; }
        h1{ margin-bottom:20px; color:#333; }
        p{ font-size:16px; color:#555; margin:10px 0 30px; }
        .btn{ display:inline-block; padding:10px 20px; border-radius:6px; text-decoration:none; margin:8px; color:#fff; }
        .btn-primary{ background:#007bff; }
        .btn-primary:hover{ background:#0056b3; }
        .btn-logout{ background:#dc3545; }
        .btn-logout:hover{ background:#b52a37; }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <h1>User Dashboard</h1>
        <p>Welcome, <strong><?= htmlspecialchars($user_name) ?></strong><br>
           (<?= htmlspecialchars($user_email) ?>)</p>

        <a href="/../../../public/index.php" class="btn btn-primary">Go to Products</a>
        
        <a href="/../../../public/logout.php" class="btn btn-logout">Logout</a>
    </div>
</body>
</html>
