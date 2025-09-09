<?php
require_once __DIR__ . '/../../app/controllers/ProductController.php';
require_once __DIR__ . '/../../config/Db_Connect.php';
require_once __DIR__ . '/../controllers/UserController.php';
require_once __DIR__ . '/../middleware/auth.php';

session_start();

// Check if logged in and if admin
if (!isset($_SESSION['role'])) {
    header("Location: login.php");
    exit;
}

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../../public/index.php");
    exit;
}

// Check for a form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new ProductController();
    $errors = $controller->add($_POST, $_FILES);
    
    if (empty($errors)) {
        // Success: Redirect to the index page with a success flag
        header("Location: ../../public/index.php?success=1");
        exit;
    } else {
        // Failure: Store errors in the session and redirect back to the form
        $_SESSION['errors'] = $errors;
        header("Location: add_product.php");
        exit;
    }
} else {
    // This is the initial page load to display the form
    // Fetch categories and store them in the session
    $catStmt = $pdo->query("SELECT id, name, type FROM categories");
    $categories = $catStmt->fetchAll(PDO::FETCH_ASSOC);

    $categoriesByType = [
        'physical' => [],
        'digital' => []
    ];
    foreach ($categories as $cat) {
        $categoriesByType[strtolower($cat['type'])][] = $cat;
    }
    
    $_SESSION['categoriesByType'] = $categoriesByType;

    // Redirect to the pure HTML form page
    header("Location: add-product.php");
    exit;
}
?>