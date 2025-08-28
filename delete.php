<?php
require "Db_Connect.php";
require "product.php";

/* ---------- Validate Product ID ---------- */
$id = $_GET['id'] ?? null;

if (!$id || !is_numeric($id)) {
    die("Invalid request. Product ID missing or not valid.");
}

/* ---------- Delete Product ---------- */
$product = new Product($pdo);

if ($product->deleteProduct((int)$id)) {
    header("Location: index.php?msg=deleted");
    exit;
} else {
    die("Failed to delete product. (ID: $id)");
}
