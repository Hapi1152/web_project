<?php
session_start();

if (!isset($_SESSION['IS_AUTH']) || $_SESSION['IS_AUTH'] !== true) {
    header('Location: login.html');
    exit;
}

$user = [
    "id" => $_SESSION['user_id'],
    "name" => $_SESSION['login'],
    "role" => $_SESSION['roles']
];
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Личный кабинет</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <div id="content">
        <header>
            <div class="top-bar">
                <div class="logo" onclick="redirectToPage('index.php')">
                    <h1>Логотип и название сайта</h1>
                </div>
                <div class="search-bar">
                    <input type="text" placeholder="Поиск по товарам, категориям и продавцам">
                    <button>🔍</button>
                </div>
                <div class="user-options">
                    <button id="auth-button">Личный кабинет</button>
                    <button id="cart-button" onclick="redirectToAuth()" data-url="<?= $redirectUrl; ?>">
                        🛒
                        <span id="cart-count">
                            0
                        </span>
                    </button>
                </div>
            </div>
            <nav class="main-nav">
                <button class="menu-button">Каталог</button>
                <a href="#">Скидки 90%</a>
                <a href="#">Steam</a>
                <a href="#">Программы</a>
                <a href="#">Сервисы</a>
                <a href="#">PlayStation</a>
                <a href="#">Xbox</a>
            </nav>
        </header>
        <main>
            <div id="user-data">
                <h1>Личный кабинет</h1>
                <p><strong>Имя:</strong> <?= htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8') ?></p>
                <p><strong>Роли:</strong> <?= htmlspecialchars(implode(',', $user['role']), ENT_QUOTES, 'UTF-8') ?></p>
                <form action="logout.php" method="post">
                    <button id="logout-button" type="submit">Выйти</button>
                </form>
            </div>
        </main>
    </div>
    <footer>
        <div class="footer-grid">
            <div class="footer-section">
                <h3>Контактная информация</h3>
            </div>
            <div class="footer-section">
                <h3>Покупателям</h3>
                <ul>
                    <li><a href="#">Часто задаваемые вопросы</a></li>
                    <li><a href="#">Публичная оферта для покупателей</a></li>
                    <li><a href="#">Политика возврата средств</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h3>Контакты</h3>
                <ul>
                    <li><a href="#">Отзывы</a></li>
                    <li><a href="#">Политика конфиденциальности</a></li>
                    <li><a href="#">Карта сайта</a></li>
                </ul>
            </div>
        </div>
    </footer>
    <script>
        function redirectToPage(url) {
            window.location.href = url;
        }
    </script>
</body>

</html>
