<?php
// app/helper/admin_dashboard_logic.php

// This file contains the backend logic for the admin dashboard.

// The session is already started by the auth middleware.
// We only need the database connection here.
require_once __DIR__ . '/../../config/Db_Connect.php';

// Fetch admin info from the session
$adminName = $_SESSION['user']['name'];
$adminEmail = $_SESSION['user']['email'];
$adminId = $_SESSION['user']['id'];

// Count products created by this admin
$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM new_products WHERE created_by = ?");
$stmt->execute([$adminId]);
$totalProducts = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
?>