<?php
// app/middleware/auth.php

// This file checks if the user is authenticated and has 'admin' role.
// It should be included at the top of any admin-only page.

session_start();

// Ensure the session is started and a user is set
if (!isset($_SESSION['user'])) {
    header("Location: /../../public/index.php");
    exit;
}

// Check if the user's role is not 'admin'
if ($_SESSION['user']['role'] !== 'user') {
    header("Location: /../../public/index.php");
    exit;
}
?>
