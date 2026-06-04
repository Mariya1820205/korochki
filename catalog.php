<?php
require 'db.php';
$catalog = $pdo->query('SELECT * FROM catalog ORDER BY id')->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Каталог курсов</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'header.php'; ?>

<main class="container">
  <h1>Каталог курсов</h1>
  <div class="cards">
    <?php foreach ($catalog as $course): ?>
      <div class="card">
        <img src="img/<?= htmlspecialchars($course['image']) ?>" alt="">
        <div class="card-body">
          <h3><?= htmlspecialchars($course['title']) ?></h3>
          <p><?= htmlspecialchars($course['description']) ?></p>
          <p class="meta"><?= (int)$course['duration'] ?> ч · <?= (int)$course['price'] ?> ₽</p>
          <?php if (user() && !admin()): ?>
            <a href="apply.php?course=<?= (int)$course['id'] ?>" class="btn">Записаться</a>
          <?php endif; ?>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</main>

<?php include 'footer.php'; ?>
<script src="script.js"></script>
</body>
</html>
