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
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['id'])) {
    echo json_encode(["error" => 1, "errorMsg" => "Некорректные данные."], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = :id AND seller_id = :seller_id");
    $stmt->execute([
        ':id' => $data['id'],
        ':seller_id' => $user_id
    ]);
    echo json_encode(["error" => 0, "message" => "Товар удален."], JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    echo json_encode(["error" => 1, "errorMsg" => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
