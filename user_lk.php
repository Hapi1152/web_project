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
include("find_roles.php");
$user_id = $_SESSION['user_id'];
$roles = get_roles($user_id);
$isSeller = in_array('Продавец', $roles);
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Личный кабинет</title>
    <link rel="stylesheet" href="css/styles.css">
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
                    <?php if ($isSeller): ?>
                        <button id="seller-button" onclick="redirectToSeller()" data-url="<?= $redirectUrl; ?>">
                            Мои товары
                        </button>
                    <?php endif; ?>
                    <button id="auth-button">Личный кабинет</button>
                    <button id="cart-button">
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
                <div id="cart-container">
                    <h2>Моя корзина</h2>
                    <table id="cart-table" border="3">
                        <thead>
                            <tr>
                                <th>Название</th>
                                <th>Цена</th>
                                <th>Действия</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
                <div class="cart-total">
                    <p></p>
                </div>
                <div>
                    <button onclick="checkoutCart()" class="checkout-btn">Оформить заказ</button>
                </div>
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
        document.addEventListener("DOMContentLoaded", function () {
            const cartDataUrl = "get_cart_quantity.php";
            function updateCartCount() {
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
            updateCartCount();
        });
        function loadCart() {
            fetch('get_cart.php')
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        alert(data.errorMsg);
                        return;
                    }
                    const cartContainer = document.getElementById('cart-container');
                    const tableBody = document.querySelector('#cart-table tbody');
                    tableBody.innerHTML = '';

                    data.data.forEach(item => {
                        for (let i = 0; i < item.quantity; i++) {
                            const row = document.createElement('tr');
                            row.innerHTML = `
                        <td>${item.name}</td>
                        <td>${item.price}</td>
                        <td><button onclick="removeFromCart(${item.product_id})">Удалить</button></td>
                    `;
                            tableBody.appendChild(row);
                        }
                    });
                    const totalContainer = document.querySelector('.cart-total p');
                    totalContainer.innerHTML = `
                Итого:${data.total} руб.
            `;
                })
                .catch(error => {
                    console.error('Ошибка загрузки корзины:', error);
                });
        }

        function removeFromCart(productId) {
            fetch('remove_from_cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `product_id=${productId}`
            })
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        alert(data.errorMsg);
                        return;
                    }
                    loadCart();
                })
                .catch(error => {
                    console.error('Ошибка удаления из корзины:', error);
                });
        }
        function checkoutCart() {
            fetch('checkout_cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        alert(data.errorMsg);
                        return;
                    }
                    alert(data.message);
                    loadCart();
                })
                .catch(error => {
                    console.error('Ошибка оформления заказа:', error);
                });
        }
        loadCart();
    </script>
</body>

</html>
