<?php
require 'db.php';

$login = '';
$err = '';
$success = isset($_GET['registered']) ? 'Регистрация прошла успешно! Войдите в систему.' : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $login = trim($_POST['login'] ?? '');
  $pass = $_POST['password'] ?? '';

  $stmt = $pdo->prepare('SELECT * FROM users WHERE login = ?');
  $stmt->execute([$login]);
  $foundUser = $stmt->fetch();

  if ($foundUser && password_verify($pass, $foundUser['password_hash'])) {
    session_regenerate_id(true); // защита от session fixation
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
  <div class="form-box">
    <h1>Вход</h1>

    <?php if ($success): ?>
      <div class="toast"><?= $success ?></div>
    <?php endif; ?>

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
  </div>
</main>

<?php include 'footer.php'; ?>
</body>
</html>
