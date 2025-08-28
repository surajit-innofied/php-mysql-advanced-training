<?php
$host = "127.0.0.1";      // or "localhost"
$db   = "surajit";     // your database name
$user = "root";           // your MySQL user
$pass = "Surajit@2003";  // your MySQL root password
$charset = "utf8mb4";

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // throw exceptions
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // fetch associative arrays
    PDO::ATTR_EMULATE_PREPARES   => false,                  // use real prepared statements
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    // if connection is not established
    die("Connection is failed: " . $e->getMessage());
}
