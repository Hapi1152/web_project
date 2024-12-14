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

if (!isset($data['name'], $data['price'], $data['category_name'], $data['image_url'])) {
    echo json_encode(["error" => 1, "errorMsg" => "Некорректные данные."], JSON_UNESCAPED_UNICODE);
    exit;
}
$stmt = $pdo->prepare("SELECT id FROM categories WHERE category_name = :category_name LIMIT 1");
$stmt->execute([':category_name' => $data['category_name']]);
$category = $stmt->fetch(PDO::FETCH_ASSOC);

$category_id = $category['id'];
try {
    $stmt = $pdo->prepare("UPDATE products
                           SET name = :name, price = :price, category_id = :category_id, image_url = :image_url
                           WHERE id = :id AND seller_id = :seller_id");
    $stmt->execute([
        ':name' => $data['name'],
        ':price' => $data['price'],
        ':category_id' => $category_id,
        ':image_url' => $data['image_url'],
        ':id' => $data['id'],
        ':seller_id' => $user_id
    ]);
    echo json_encode(["error" => 0, "message" => "Товар обновлен."], JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    echo json_encode(["error" => 1, "errorMsg" => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
