<?php
require_once 'db_connect.php';

$stmt = $pdo->prepare("SELECT category_name FROM categories ORDER BY category_name");
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($categories, JSON_UNESCAPED_UNICODE);
