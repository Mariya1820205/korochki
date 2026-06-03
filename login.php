<?php
require 'db.php';

$login = '';
$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $login = trim($_POST['login'] ?? '');
  $pass = $_POST['password'] ?? '';

  $stmt = $pdo->prepare('SELECT * FROM users WHERE login = ?');
  $stmt->execute([$login]);
  $foundUser = $stmt->fetch();

  if ($foundUser && password_verify($pass, $foundUser['password'])) {
    $_SESSION['user'] = $foundUser;
    if ($foundUser['role'] === 'admin') {
      header('Location: admin.php');
    } else {
      header('Location: orders.php');
    }
    exit;
  }

  $err = 'Неверный логин или пароль';
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Вход</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'header.php'; ?>

<main class="container">
  <h1>Вход</h1>
  <form method="post" class="form">
    <label>Логин
      <input name="login" value="<?= htmlspecialchars($login) ?>">
    </label>

    <label>Пароль
      <input type="password" name="password">
    </label>

    <span class="err"><?= $err ?></span>

    <button>Войти</button>
    <a href="register.php">Еще не зарегистрированы? Регистрация</a>
  </form>
</main>

<?php include 'footer.php'; ?>
</body>
</html>
