<?php
$host = 'localhost';
$db   = 'surajit';
$user = 'root'; // change if needed
$pass = 'Surajit@2003';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
} catch (PDOException $e) {
    die("Database Connection Failed: " . $e->getMessage());
}
?>