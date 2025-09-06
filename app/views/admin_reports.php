<?php
// admin/reports.php
session_start();
require_once __DIR__ . '/../../config/Db_Connect.php';

// ✅ Ensure only admins can access
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../../public/index.php");
    exit;
}

// ================== REPORT QUERIES ==================

// Total Orders
$stmt = $pdo->query("SELECT COUNT(*) as total_orders FROM orders");
$totalOrders = $stmt->fetch(PDO::FETCH_ASSOC)['total_orders'];

// Total Revenue (only paid orders)
$stmt = $pdo->query("SELECT SUM(total_amount) as revenue FROM orders WHERE statuss = 'paid'");
$totalRevenue = $stmt->fetch(PDO::FETCH_ASSOC)['revenue'] ?? 0;

// Top-selling products
$stmt = $pdo->query("
    SELECT p.name, SUM(oi.quantity) as total_sold
    FROM order_items oi
    JOIN new_products p ON oi.product_id = p.id
    JOIN orders o ON oi.order_id = o.id
    WHERE o.statuss = 'paid'
    GROUP BY oi.product_id
    ORDER BY total_sold DESC
    LIMIT 5
");
$topProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Orders by status
$stmt = $pdo->query("
    SELECT statuss, COUNT(*) as count 
    FROM orders 
    GROUP BY statuss
");
$statusCounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Reports</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f4f6f9;
      margin: 0;
      padding: 20px;
    }
    .container {
      max-width: 900px;
      margin: 0 auto;
      background: #fff;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0px 4px 10px rgba(0,0,0,0.1);
    }
    h2 { color: #333; }
    .card {
      background: #f9f9f9;
      padding: 15px;
      margin: 10px 0;
      border-radius: 6px;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin: 15px 0;
    }
    table th, table td {
      padding: 10px;
      border: 1px solid #ddd;
      text-align: left;
    }
    .btn {
      display: inline-block;
      padding: 10px 20px;
      background: #04AA6D;
      color: #fff;
      border-radius: 6px;
      text-decoration: none;
      margin-top: 10px;
    }
    .btn:hover { background: #028a56; }
  </style>
</head>
<body>
  <div class="container">
    <h2>📊 Admin Reports & Analytics</h2>
    <a href="generate_report_pdf.php" class="btn" target="_blank">⬇ Download PDF</a>

    <div class="card">
      <h3>Total Orders</h3>
      <p><strong><?= $totalOrders ?></strong></p>
    </div>

    <div class="card">
      <h3>Total Revenue</h3>
      <p><strong>₹<?= number_format($totalRevenue, 2) ?></strong></p>
    </div>

    <div class="card">
      <h3>Top Selling Products</h3>
      <table>
        <tr><th>Product</th><th>Units Sold</th></tr>
        <?php foreach ($topProducts as $p): ?>
          <tr>
            <td><?= htmlspecialchars($p['name']) ?></td>
            <td><?= $p['total_sold'] ?></td>
          </tr>
        <?php endforeach; ?>
      </table>
    </div>

    <div class="card">
      <h3>Orders by Status</h3>
      <table>
        <tr><th>Status</th><th>Count</th></tr>
        <?php foreach ($statusCounts as $s): ?>
          <tr>
            <td><?= htmlspecialchars($s['statuss']) ?></td>
            <td><?= $s['count'] ?></td>
          </tr>
        <?php endforeach; ?>
      </table>
    </div>

    <a href="http://localhost:8000/public/index.php" class="btn">⬅ Back to Dashboard</a>
  </div>
</body>
</html>
