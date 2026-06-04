<header class="nav">
  <a href="index.php" class="logo">Учись.рф</a>

  <button class="burger" onclick="document.querySelector('.nav nav').classList.toggle('open')">☰</button>

  <nav>
    <a href="index.php">Главная</a>
    <a href="catalog.php">Каталог</a>
    <?php if (admin()): ?>
      <a href="admin.php">Админ-панель</a>
      <a href="logout.php">Выход (<?= htmlspecialchars(user()['login']) ?>)</a>
    <?php elseif (user()): ?>
      <a href="apply.php">Подать заявку</a>
      <a href="orders.php">Мои заявки</a>
      <a href="logout.php">Выход (<?= htmlspecialchars(user()['login']) ?>)</a>
    <?php else: ?>
      <a href="login.php">Вход</a>
      <a href="register.php">Регистрация</a>
    <?php endif; ?>
  </nav>
</header>
