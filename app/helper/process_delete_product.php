<?php
// app/helper/delete_product_logic.php

require_once __DIR__ . '/../../app/controllers/ProductController.php';

session_start();

// Ensure the request is a POST request and the delete action is set
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_product') {
    // Check if the user is an admin before proceeding
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
        header("Location: /../../../public/index.php");
        exit;
    }

    $controller = new ProductController();
    $id = (int)$_POST['delete_id'];
    $result = $controller->delete($id);

    if ($result) {
        // Redirect on successful deletion
        header("Location: /../../../public/index.php?msg=deleted");
        exit;
    } else {
        // Redirect with an error message on failure
        header("Location: /../../../public/index.php?msg=delete_failed");
        exit;
    }
} else {
    // Redirect if accessed directly without a proper POST request
    header("Location: /../../../public/index.php");
    exit;
}
