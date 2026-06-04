<?php
require 'db.php';
if (!admin()) { header('Location: login.php'); exit; }

$ordersCount = $pdo->query('SELECT COUNT(*) FROM orders')->fetchColumn();
$usersCount = $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
$catalogCount = $pdo->query('SELECT COUNT(*) FROM catalog')->fetchColumn();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Админ-панель</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'header.php'; ?>

<main class="container">
  <?php include 'admin_tabs.php'; ?>
  <h1>Админ-панель</h1>
  <div class="cards">
    <a href="admin_orders.php" class="card admin-card">
      <div class="card-body">
        <h3>Заявки</h3>
        <p class="big"><?= $ordersCount ?></p>
        <p>Управление заявками, смена статуса, фильтр.</p>
      </div>
    </a>
    <a href="admin_catalog.php" class="card admin-card">
      <div class="card-body">
        <h3>Каталог</h3>
        <p class="big"><?= $catalogCount ?></p>
        <p>Добавление, редактирование и удаление курсов.</p>
      </div>
    </a>
    <a href="admin_users.php" class="card admin-card">
      <div class="card-body">
        <h3>Пользователи</h3>
        <p class="big"><?= $usersCount ?></p>
        <p>Управление пользователями и ролями.</p>
      </div>
    </a>
  </div>
</main>

<?php include 'footer.php'; ?>
</body>
</html>
