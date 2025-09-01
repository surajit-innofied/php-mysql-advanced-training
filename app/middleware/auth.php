<?php
// auth.php → to secure private pages
session_start();

if (!isset($_SESSION['user'])) {
    // User is not logged in → redirect to login page
    header("Location: ../../public/login.php?error=unauthorized");
    exit;
}

?>