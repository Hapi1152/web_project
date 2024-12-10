<?php
session_start();
if (array_key_exists('IS_AUTH', $_SESSION)) {
    header('Location: index.php');
    die();
}
try {

    $dsn = "pgsql:host=localhost;port=5432;dbname=postgres";
    $conn = new PDO($dsn, "postgres", "7746597Ss");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "" . $e->getMessage();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login']);
    $password = trim($_POST['password']);

    if (!$login || !$password) {
        echo "Пожалуйста, заполните все поля!";
        exit;
    }

    try {
        $stmt = $conn->prepare("select id, password from users where login = :login");
        $stmt->bindParam(':login', $login, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && $password == $user['password']) {
            include("find_roles.php");
            $_SESSION['IS_AUTH'] = true;
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['login'] = $login;
            $_SESSION['roles'] = get_roles($user["id"]);
            header("Location: index.php");
            exit;
        } else {
            echo "Неверный логин или пароль!";
        }
    } catch (PDOException $e) {
        echo "Ошибка: " . $e->getMessage();
    }
} else {
    echo "Некорректный метод запроса.";
}
