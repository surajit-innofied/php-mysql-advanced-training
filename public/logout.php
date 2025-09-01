<?php
// public/logout.php
session_start();

// clear session array
$_SESSION = [];

// destroy session cookie (best practice)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params['path'], $params['domain'],
        $params['secure'], $params['httponly']
    );
}

// destroy session
session_destroy();

// redirect to public index
header('Location: index.php');
exit;
