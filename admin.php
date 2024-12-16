<?php
session_start();
require_once 'db_connect.php';
if (!isset($_SESSION['IS_AUTH']) || $_SESSION['IS_AUTH'] !== true) {
    header('Location: login.html');
    exit;
}

$user = [
    "id" => $_SESSION['user_id'],
    "name" => $_SESSION['login'],
    "role" => $_SESSION['roles']
];
if (!in_array('Администратор', $_SESSION['roles'])) {
    header('Location: index.php');
    exit;
}
$sql = "SELECT users.login AS user_login, users.name AS user_name,
        STRING_AGG(roles.name, ', ') AS roles_list
        FROM users
        JOIN user_roles ON users.id = user_roles.user_id
        JOIN roles ON user_roles.role_id = roles.id
        GROUP BY users.id, users.login, users.name;";
try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Ошибка выполнения запроса: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Личный кабинет</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/seller.css">
</head>

<body>
    <div id="content">
        <header>
            <div class="top-bar">
                <div class="logo" onclick="redirectToPage('index.php')">
                    <img src="goodgames_logo.png" alt="Логотип и название сайта">
                </div>
                <div class="search-bar">
                    <input type="text" placeholder="Поиск по товарам, категориям и продавцам">
                    <button>🔍</button>
                </div>
                <div class="user-options">
                    <button id="seller-button">
                        Мои товары
                    </button>
                    <button id="auth-button" onclick="redirectToPage('user_lk.php')">Личный кабинет</button>
                    <button id="cart-button" onclick="redirectToPage('user_lk.php')">
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

            <div id="seller-tools">
                <h1>Панель администратора</h1>
                <h2>Все пользователи</h2>
                <table class="users-table" border="3">
                    <thead>
                        <tr>
                            <th>Логин</th>
                            <th>Имя</th>
                            <th>Роли</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($result as $row): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['user_login']); ?></td>
                                <td><?php echo htmlspecialchars($row['user_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['roles_list']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <h2>Все товары</h2>
                <table id="product-table" border="3">
                    <thead>
                        <tr>
                            <th>Название</th>
                            <th>Цена</th>
                            <th>Категория</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
                <form id="add-product-form" style="display: none;">
                    <h3>Добавить товар</h3>
                    <label for="add-product-name">Название товара</label><br>
                    <input type="text" id="add-product-name" name="name" required><br>

                    <label for="add-product-price">Цена</label><br>
                    <input type="number" id="add-product-price" name="price" required><br>

                    <label for="add-product-category">Категория</label><br>
                    <select id="add-product-category" name="category_name" required></select><br>

                    <label for="add-product-image-url">URL изображения</label><br>
                    <input type="text" id="add-product-image-url" name="image_url" required><br>

                    <button type="submit">Добавить товар</button>
                </form>
                <form id="edit-product-form" style="display: none;">
                    <h3>Редактировать товар</h3>
                    <input type="hidden" id="edit-product-id" name="id"><br>

                    <label for="edit-product-name">Название товара</label><br>
                    <input type="text" id="edit-product-name" name="name" required><br>

                    <label for="edit-product-price">Цена</label><br>
                    <input type="number" id="edit-product-price" name="price" required><br>

                    <label for="edit-product-category">Категория</label><br>
                    <select id="edit-product-category" name="category_name" required></select><br>

                    <label for="edit-product-image-url">URL изображения</label><br>
                    <input type="text" id="edit-product-image-url" name="image_url" required><br>

                    <button type="submit">Сохранить изменения</button>
                </form>

                <div id="message"></div>
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
        function loadSellerProducts() {
            fetch('get_products_admin.php')
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        alert(data.errorMsg);
                        return;
                    }

                    const tableBody = document.getElementById('product-table').querySelector('tbody');
                    tableBody.innerHTML = '';

                    data.data.forEach(product => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                        <td>${product.name}</td>
                        <td>${product.price} руб.</td>
                        <td>${product.category_name}</td>
                        <td>
                        <button class="edit-button" data-product='${JSON.stringify(product)}'>Редактировать</button>
                        <button onclick = "deleteProduct(${product.id})" > Удалить</button >
                        </td >
                 `;
                        tableBody.appendChild(row);
                    });

                    document.querySelectorAll('.edit-button').forEach(button => {
                        button.addEventListener('click', () => {
                            const product = JSON.parse(button.getAttribute('data-product'));
                            showEditForm(product);
                        });
                    });
                })
                .catch(error => console.error('Ошибка загрузки товаров:', error));
        }
        document.getElementById('add-product-form').addEventListener('submit', function (event) {
            event.preventDefault();

            const name = document.getElementById('add-product-name').value;
            const price = document.getElementById('add-product-price').value;
            const categoryName = document.getElementById('add-product-category').value;
            const imageUrl = document.getElementById('add-product-image-url').value;

            fetch('add_product.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ name, price, category_name: categoryName, image_url: imageUrl })
            })
                .then(response => response.json())
                .then(data => {
                    document.getElementById('message').textContent = data.error ? `Ошибка: ${data.errorMsg} ` : data.message;
                    if (!data.error) {
                        document.getElementById('add-product-form').reset();
                        document.getElementById('add-product-form').style.display = 'none'
                        loadSellerProducts();
                    }
                })
                .catch(error => console.error('Ошибка при добавлении товара:', error));
        });
        document.getElementById('edit-product-form').addEventListener('submit', function (event) {
            event.preventDefault();

            const id = document.getElementById('edit-product-id').value;
            const name = document.getElementById('edit-product-name').value;
            const price = document.getElementById('edit-product-price').value;
            const categoryName = document.getElementById('edit-product-category').value;
            const imageUrl = document.getElementById('edit-product-image-url').value;
            console.log(id, name, price, categoryName, imageUrl);

            fetch('edit_product.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id, name, price, category_name: categoryName, image_url: imageUrl })
            })
                .then(response => response.json())
                .then(data => {
                    document.getElementById('message').textContent = data.error ? `Ошибка: ${data.errorMsg}` : data.message;
                    if (!data.error) {
                        document.getElementById('edit-product-form').reset();
                        document.getElementById('edit-product-form').style.display = 'none';
                        loadSellerProducts();
                    }
                })
                .catch(error => console.error('Ошибка при редактировании товара:', error));
        });

        function showEditForm(product) {
            document.getElementById('message').textContent = '';
            document.getElementById('edit-product-form').style.display = 'block';
            document.getElementById('add-product-form').style.display = 'none';

            document.getElementById('edit-product-id').value = product.id;
            document.getElementById('edit-product-name').value = product.name;
            document.getElementById('edit-product-price').value = product.price;
            document.getElementById('edit-product-category').value = product.category_name;
            document.getElementById('edit-product-image-url').value = product.image_url;
            document.getElementById('edit-product-form').scrollIntoView({ behavior: 'smooth' });
        }


        document.addEventListener('DOMContentLoaded', function () {
            fetch('get_categories.php')
                .then(response => response.json())
                .then(categories => {
                    const categorySelectAdd = document.getElementById('add-product-category');
                    const categorySelectEdit = document.getElementById('edit-product-category');
                    categories.forEach(category => {
                        const option1 = document.createElement('option');
                        const option2 = document.createElement('option');
                        option1.value = category.category_name;
                        option1.textContent = category.category_name;
                        option2.value = category.category_name;
                        option2.textContent = category.category_name;
                        categorySelectAdd.appendChild(option1);
                        categorySelectEdit.appendChild(option2);
                    });
                })
                .catch(error => console.error('Ошибка при загрузке категорий:', error));
        });

        function deleteProduct(productId) {
            fetch('delete_product.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: productId })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        alert(data.errorMsg);
                    } else {
                        alert(data.message);
                        loadSellerProducts();
                    }
                })
                .catch(error => console.error('Ошибка удаления товара:', error));
        }

        loadSellerProducts();
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
    </script>
</body>

</html>
