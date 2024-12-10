<?php
session_start();

header('Content-Type: application/json');

if (!array_key_exists('IS_AUTH', $_SESSION)) {
    echo json_encode([
        'success' => false,
        'message' => 'Для добавления в корзину необходимо авторизоваться.'
    ]);
    die();
}

if (!isset($_POST['product_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Не указан товар для добавления в корзину.'
    ]);
    exit;
}
$productId = intval($_POST['product_id']);

if ($productId > 0) {
    echo json_encode([
        'success' => true,
        'message' => 'Товар успешно добавлен в корзину.'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Произошла ошибка при добавлении товара в корзину.'
    ]);
}
?>