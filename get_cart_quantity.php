<?php
session_start();
try {
    $dsn = "pgsql:host=localhost;port=5432;dbname=postgres";
    $pdo = new PDO($dsn, "postgres", "7746597Ss");
    if (!isset($_SESSION['user_id'])) {
        // Если пользователь не авторизован, возвращаем "unauthorized"
        echo json_encode(['status' => 'unauthorized']);
        exit;
    }
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Ошибка подключения: " . $e->getMessage();
    exit;
}

try {
    $user_id = $_SESSION['user_id'];

    $stmt = $pdo->prepare('SELECT SUM(quantity) AS total_quantity FROM cart WHERE user_id = :user_id');
    $stmt->execute(['user_id' => $user_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode(['total_quantity' => $result['total_quantity'] ?? 0]);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
