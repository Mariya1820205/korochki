<?php require 'db.php'; ?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Корочки.есть</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'header.php'; ?>

<main class="container">
  <h1>Онлайн-курсы ДПО</h1>
  <p>Запишитесь на курсы дополнительного профессионального образования.</p>

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
    <div class="card-course">
      <img src="img/image02.jpg" alt="">
      <h3>Основы алгоритмизации и программирования</h3>
    </div>
    <div class="card-course">
      <img src="img/image05.jpg" alt="">
      <h3>Основы веб-дизайна</h3>
    </div>
    <div class="card-course">
      <img src="img/image06.jpg" alt="">
      <h3>Основы проектирования баз данных</h3>
    </div>
  </div>
</main>

<?php include 'footer.php'; ?>
<script src="script.js"></script>
</body>
</html>
