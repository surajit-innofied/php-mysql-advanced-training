<?php
require "Db_Connect.php";
require "product.php";

$id = $_GET['id'] ?? null;

if (!$id || !is_numeric($id)) {
    die("Invalid request. Product ID missing or not valid.");
}

$product = new Product($pdo);

if ($product->deleteProduct((int)$id)) {
    header("Location: index.php?msg=deleted");
    exit;
} else {
    die("Failed to delete product. (ID: $id)");
}
