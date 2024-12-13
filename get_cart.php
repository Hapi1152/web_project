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

$sql = "SELECT c.product_id, p.name, p.price, c.quantity
        FROM cart c
        JOIN products p ON c.product_id = p.id
        WHERE c.user_id = :user_id";

$sql_total = "SELECT SUM(p.price * c.quantity) AS total
              FROM cart c
              JOIN products p ON c.product_id = p.id
              WHERE c.user_id = :user_id";

$stmt = $pdo->prepare($sql);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

$stmt_total = $pdo->prepare($sql_total);
$stmt_total->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$result_cart = json_decode(get_json($stmt), true);
$stmt_total->execute();
$total = $stmt_total->fetch(PDO::FETCH_ASSOC)['total'];

$result_cart['total'] = $total ?? 0; 
echo json_encode($result_cart, JSON_UNESCAPED_UNICODE);
