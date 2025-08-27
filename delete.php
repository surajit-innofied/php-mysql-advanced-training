<?php
require "Db_Connect.php";
require "product.php";

$id = $_GET['id'] ?? null;

if (!$id || !is_numeric($id)) {
    die(" Invalid request. Product ID missing or not valid.");
}

$product = new Product($pdo);
$product->id = (int)$id; // cast to int for safety

if ($product->deleteProduct()) {
    header("Location: index.php?msg=deleted");
    exit;
} else {
    die("Failed to delete product. (ID: $id)");
}
