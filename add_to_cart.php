<?php
session_start();
header('Content-Type: application/json');
try {
    $dsn = "pgsql:host=localhost;port=5432;dbname=postgres";
    $pdo = new PDO($dsn, "postgres", "7746597Ss");

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Ошибка подключения: " . $e->getMessage();
    exit;
}

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
$product_id = intval($_POST['product_id']);

if ($product_id > 0) {
    $user_id = $_SESSION['user_id'];
    try {
        $stmt = $pdo->prepare("INSERT INTO cart (user_id, product_id)
                                      VALUES (:user_id, :product_id)
                                      ON CONFLICT (user_id, product_id) DO UPDATE
                                      SET quantity = cart.quantity + EXCLUDED.quantity;
                                      ");
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
