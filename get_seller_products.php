<?php
require_once 'db_connect.php';

session_start();

if (!array_key_exists('IS_AUTH', $_SESSION)) {
    echo json_encode([
        "error" => 1,
        "errorMsg" => "Вы не авторизованы."
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT p.id, p.name, p.price, p.image_url, c.category_name AS category_name
        FROM products p
        JOIN categories c ON p.category_id = c.id
        WHERE p.seller_id = :seller_id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':seller_id', $user_id, PDO::PARAM_INT);

echo get_json($stmt);
