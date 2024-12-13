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
$product_id = $_POST['product_id'] ?? null;

if (!$product_id) {
    echo json_encode([
        "error" => 1,
        "errorMsg" => "ID товара не указан."
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $pdo->beginTransaction();
    $stmt = $pdo->prepare("SELECT quantity FROM cart WHERE user_id = :user_id AND product_id = :product_id");
    $stmt->execute([
        ':user_id' => $user_id,
        ':product_id' => $product_id
    ]);
    $cart_item = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$cart_item) {
        throw new Exception("Товар не найден в корзине.");
    }

    if ($cart_item['quantity'] > 1) {
        $stmt = $pdo->prepare("UPDATE cart SET quantity = quantity - 1 WHERE user_id = :user_id AND product_id = :product_id");
        $stmt->execute([
            ':user_id' => $user_id,
            ':product_id' => $product_id
        ]);
    } else {
        $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = :user_id AND product_id = :product_id");
        $stmt->execute([
            ':user_id' => $user_id,
            ':product_id' => $product_id
        ]);
    }

    $pdo->commit();
    echo json_encode(["error" => 0, "message" => "Товар успешно удален."], JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(["error" => 1, "errorMsg" => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
