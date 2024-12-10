<?php
session_start();
try {
    $dsn = "pgsql:host=localhost;port=5432;dbname=postgres";
    $conn = new PDO($dsn, "postgres", "7746597Ss");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Ошибка подключения: " . $e->getMessage();
    exit;
}
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
    $user_id = $_SESSION['user_id'];
    try {
        $stmt = $pdo->prepare("INSERT INTO cart (user_id, product_id) VALUES (:user_id, :product_id)");
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);

        $stmt->execute();
        echo json_encode([
            'success' => true,
            'message' => 'Товар успешно добавлен в корзину.'
        ]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Ошибка при добавлении товара: ' . $e->getMessage()]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Произошла ошибка при добавлении товара в корзину.'
    ]);
}
?>
