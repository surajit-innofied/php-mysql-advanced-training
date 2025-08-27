<?php
require "Db_Connect.php";

$id = $_GET["id"];
$stmt = $pdo->prepare("DELETE FROM new_products WHERE id=?");
$stmt->execute([$id]);

header("Location: index.php");
exit;
?>
