<?php
// admin/reports.php
session_start();
require_once __DIR__ . '/../../../config/Db_Connect.php';

// ⬇️ Include the new authentication middleware at the top
require_once __DIR__ . '/../../middleware/auth.php';

// ⬇️ Include the new helper file to get all report data
require_once __DIR__ . '/../../helper/admin_reports_helper.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Admin Reports</title>
  <link rel="stylesheet" href="/../../../public/css/admin_reports.css">
</head>

<body>
  <div class="container">
    <h2>📊 Admin Reports & Analytics</h2>
    <a href="/../app/helper/generate_report_pdf.php" class="btn" target="_blank">⬇ Download PDF</a>

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
        <tr>
          <th>Product</th>
          <th>Units Sold</th>
        </tr>
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
        <tr>
          <th>Status</th>
          <th>Count</th>
        </tr>
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