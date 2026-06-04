<?php
require 'db.php';
$catalog = $pdo->query('SELECT * FROM catalog ORDER BY id')->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Учись.рф — онлайн-курсы ДПО</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'header.php'; ?>

<section class="hero">
  <div class="hero-text">
    <h1>Учись.рф</h1>
    <p>Современные онлайн-курсы дополнительного профессионального образования. Учись в удобном темпе и получай документ установленного образца.</p>
    <a href="catalog.php" class="btn">Смотреть каталог</a>
  </div>
</section>

<main class="container">
  <h2>Популярные курсы</h2>
  <div class="slider" id="slider">
    <img src="img/image08.webp" class="slide active" alt="">
    <img src="img/image09.webp" class="slide" alt="">
    <img src="img/image10.webp" class="slide" alt="">
    <img src="img/image12.webp" class="slide" alt="">
    <button class="prev" onclick="move(-1)">‹</button>
    <button class="next" onclick="move(1)">›</button>
  </div>

  <h2>Наши курсы</h2>
  <div class="cards">
    <?php foreach ($catalog as $course): ?>
      <div class="card">
        <img src="img/<?= htmlspecialchars($course['image']) ?>" alt="">
        <div class="card-body">
          <h3><?= htmlspecialchars($course['title']) ?></h3>
          <p><?= htmlspecialchars($course['description']) ?></p>
          <p class="meta"><?= (int)$course['duration'] ?> ч · <?= (int)$course['price'] ?> ₽</p>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <h2>Почему мы</h2>
  <div class="features">
    <div class="feature"><h3>Документ</h3><p>Удостоверение о повышении квалификации установленного образца.</p></div>
    <div class="feature"><h3>Практика</h3><p>Реальные задачи и проекты в портфолио уже во время обучения.</p></div>
    <div class="feature"><h3>Поддержка</h3><p>Куратор и преподаватели отвечают в течение дня.</p></div>
  </div>
</main>

<?php include 'footer.php'; ?>
<script src="script.js"></script>
</body>
</html>
