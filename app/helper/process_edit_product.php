<?php
require_once __DIR__ . '/../../app/controllers/ProductController.php';
require_once __DIR__ . '/../../config/Db_Connect.php';
require_once __DIR__ . '/../middleware/auth.php';

session_start();

// Check if logged in
if (!isset($_SESSION['role'])) {
    header("Location: ../../public/login.php");
    exit;
}

// Allow only admin
if ($_SESSION['role'] !== 'admin') {
    header("Location: ../../public/index.php");
    exit;
}

$controller = new ProductController();
$id = $_GET['id'] ?? null;

if (!$id) {
    die("Invalid product ID");
}

// Handle POST request (form submission from the view)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = $controller->edit($id, $_POST, $_FILES);
    if (empty($errors)) {
        header("Location: ../../public/index.php?success=2");
        exit;
    } else {
        $_SESSION['edit_errors'] = $errors;
        // Keep form data on error
        $_SESSION['product_data'] = $_POST;
        $_SESSION['product_data']['id'] = $id;
        header("Location: " . dirname($_SERVER['PHP_SELF']) . "/../../app/views/product/edit-product.php?id=" . $id);
        exit;
    }
}

// Handle GET request (initial page load from the edit button)
try {
    $stmt = $pdo->prepare("SELECT * FROM new_products WHERE id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        die("Product not found");
    }

    // Fetch categories for the dropdown
    $categoriesByType = $controller->getCategories();

    // Store data in session to be accessed by the view
    $_SESSION['product_data'] = $product;
    $_SESSION['categories'] = $categoriesByType;

    // Redirect to the frontend view
    header("Location: " . dirname($_SERVER['PHP_SELF']) . "/../../app/views/product/edit-product.php?id=" . $id);
    exit;
} catch (PDOException $e) {
    // If a database error occurs, store the error and redirect
    $_SESSION['edit_errors'] = ["Database error: " . $e->getMessage()];
    header("Location: " . dirname($_SERVER['PHP_SELF']) . "/../../app/views/product/edit-product.php?id=" . $id);
    exit;
}
