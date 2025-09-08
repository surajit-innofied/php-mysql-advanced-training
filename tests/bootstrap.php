<?php
// tests/bootstrap.php
require __DIR__ . '/../vendor/autoload.php';

// DB config for test DB
$DB_HOST = '127.0.0.1';
$DB_NAME = 'surajit_test';
$DB_USER = 'root';
$DB_PASS = 'Surajit@2003'; // put password if you have one

$dsn = "mysql:host={$DB_HOST};dbname={$DB_NAME};charset=utf8mb4";

try {
    $testPdo = new PDO($dsn, $DB_USER, $DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    fwrite(STDERR, "Could not connect to test DB: " . $e->getMessage() . PHP_EOL);
    exit(1);
}

// Assign so your app uses test PDO
$GLOBALS['pdo'] = $testPdo;

// start clean session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
