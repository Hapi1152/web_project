<?php

try {
    session_start();
    $isLoggedIn = isset($_SESSION['IS_AUTH']);
    $buttonText = $isLoggedIn ? "Личный кабинет" : "Авторизация";
    $redirectUrl = $isLoggedIn ? "user_lk.php" : "login.html";
    $dsn = "pgsql:host=localhost;port=5432;dbname=postgres";
    $conn = new PDO($dsn, "postgres", "7746597Ss");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Ошибка подключения: " . $e->getMessage();
    exit;
}
try {
    $query = "SELECT p.id, p.name, p.price, p.image_url, c.category_name AS category_name FROM products p
              JOIN categories c ON p.category_id = c.id";
    $stmt = $conn->query($query);
    if (!$stmt) {
        echo "Ошибка запроса: " . $conn->errorInfo()[2];
        exit;
    }
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Ошибка выполнения запроса: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Главная страница</title>
    <link rel="stylesheet" href="css/styles.css">
</head>

<body>
    <div id="content">
        <header>
            <div class="top-bar">
                <div class="logo">
                    <h1>Логотип и название сайта</h1>
                </div>
                <div class="search-bar">
                    <input type="text" placeholder="Поиск по товарам, категориям и продавцам">
                    <button>🔍</button>
                </div>
                <div class="user-options">
                    <button id="seller-button" onclick="redirectToSeller()" data-url="<?= $redirectUrl; ?>">
                        Мои товары
                    </button>
                    <button id="auth-button" onclick="redirectToAuth()" data-url="<?= $redirectUrl; ?>">
                        <?= $buttonText; ?>
                    </button>
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
            <section class="popular-section">
                <h2>Популярные игры и сервисы</h2>
                <div class="game-grid">
                    <div class="game-item">
                        <img src="steam-logo.jpg" alt="Пополнение Steam">
                        <p>Пополнение Steam</p>
                    </div>
                    <div class="game-item">
                        <img src="riot-arcane.jpg" alt="Riot Games: Arcane">
                        <p>Riot Games: Arcane</p>
                    </div>
                    <div class="game-item">
                        <img src="cod-black-ops.jpg" alt="Call of Duty: Black Ops 6">
                        <p>Call of Duty: Black Ops 6</p>
                    </div>
                    <div class="game-item">
                        <img src="microsoft.jpg" alt="Microsoft">
                        <p>Microsoft</p>
                    </div>
                </div>
            </section>
            <section>
                <div class="product-grid">
                    <?php
                    if (!empty($products)) {
                        foreach ($products as $row) {
                            echo "<div class='product-card'>";
                            echo "<img src='" . htmlspecialchars($row['image_url']) . "' alt='" . htmlspecialchars($row['name']) . "'>";
                            echo "<span class='category'>" . htmlspecialchars($row['category_name']) . "</span>";
                            echo "<p class='price'>" . htmlspecialchars($row['price']) . " руб.</p>";
                            echo "<h3>" . htmlspecialchars($row['name']) . "</h3>";
                            echo "<button class='buy-button' onclick='addToCart(" . $row['id'] . ");'>Купить</button>";
                            echo "</div>";
                        }
                    } else {
                        echo "Товары не найдены";
                    }
                    ?>

                </div>
                <script>
                    function updateCartCount() {
                        const cartDataUrl = "get_cart_quantity.php";
                        fetch(cartDataUrl)
                            .then(response => response.json())
                            .then(data => {

                                if (data.error) {
                                    console.error("Ошибка: " + data.error);
                                    return;
                                }

                                const cartCountElement = document.getElementById("cart-count");
                                if (data.status === 'unauthorized') {
                                    cartCountElement.style.display = "none";
                                }
                                else if (data.total_quantity > 0) {
                                    cartCountElement.textContent = data.total_quantity;
                                    cartCountElement.style.display = "block";
                                } else {
                                    cartCountElement.style.display = "none";
                                }
                            })
                            .catch(error => console.error("Ошибка при получении данных корзины: ", error));
                    }
                    function addToCart(productId) {
                        fetch('add_to_cart.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            },
                            body: `product_id=${productId}`
                        })
                            .then(response => response.json())
                            .then(data => {
                                updateCartCount();
                            })
                            .catch(error => {
                                console.error('Ошибка при добавлении в корзину:', error);
                                console.log(productId)
                                alert('Произошла ошибка. Попробуйте снова.');
                            });
                    }
                    function redirectToAuth() {
                        const authButton = document.getElementById('auth-button');
                        const redirectUrl = authButton.getAttribute('data-url');
                        window.location.href = redirectUrl;
                    }
                    function redirectToSeller() {
                        window.location.href = "seller.php"
                    }
                    document.addEventListener("DOMContentLoaded", function () {
                        updateCartCount();
                    });

                </script>
            </section>
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
</body>

</html>
