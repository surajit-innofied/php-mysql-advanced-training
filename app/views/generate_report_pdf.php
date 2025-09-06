<?php
// admin/generate_report_pdf.php
session_start();
require_once __DIR__ . '/../../config/Db_Connect.php';
require_once __DIR__ . '/../../vendor/autoload.php';

// Check if Dompdf is loaded
if (!class_exists('Dompdf\Dompdf')) {
    die('Dompdf library not found. Please run "composer require dompdf/dompdf" and check autoload path.');
}

use Dompdf\Dompdf;
use Dompdf\Options;

// ✅ Ensure only admins can access
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../../public/index.php");
    exit;
}



// ---- Fetch Report Data ----
$stmt = $pdo->query("SELECT COUNT(*) as total_orders FROM orders");
$totalOrders = $stmt->fetch(PDO::FETCH_ASSOC)['total_orders'];

$stmt = $pdo->query("SELECT SUM(total_amount) as revenue FROM orders WHERE statuss = 'paid'");
$totalRevenue = $stmt->fetch(PDO::FETCH_ASSOC)['revenue'] ?? 0;

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

// ---- Generate PDF ----
$options = new Options();
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);

$html = "
    <h2 style='text-align:center;'>Admin Report</h2>
    <p><strong>Total Orders:</strong> $totalOrders</p>
    <p><strong>Total Revenue:</strong> " . number_format($totalRevenue, 2) . "</p>

    <h3>Top Selling Products</h3>
    <table border='1' cellspacing='0' cellpadding='6' width='100%'>
        <tr>
            <th>Product</th>
            <th>Units Sold</th>
        </tr>";

foreach ($topProducts as $p) {
    $html .= "
        <tr>
            <td>{$p['name']}</td>
            <td>{$p['total_sold']}</td>
        </tr>";
}

$html .= "</table>
    <br><br>
    <p style='font-size:12px; text-align:center;'>Generated on " . date('Y-m-d H:i') . "</p>
";

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// ✅ Stream PDF in browser
$dompdf->stream("admin_report.pdf", ["Attachment" => false]);
