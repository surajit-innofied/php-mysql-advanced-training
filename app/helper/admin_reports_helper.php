<?php
require_once __DIR__ . '/../../config/Db_Connect.php';
// app/helper/admin_reports_helper.php

// This file contains all the database queries for the admin reports page.

// ================== REPORT QUERIES ==================

// Try to connect to the database and run the queries
try {
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

} catch (PDOException $e) {
    // If a database error occurs, set default values and log the error
    error_log("Database Error in admin reports: " . $e->getMessage());
    $totalOrders = "Error";
    $totalRevenue = "Error";
    $topProducts = [];
    $statusCounts = [];
}
?>
