<?php
require 'db.php';
if (!admin()) { header('Location: login.php'); exit; }

// Удаление
if (($_GET['delete'] ?? 0) > 0) {
  $stmt = $pdo->prepare('DELETE FROM catalog WHERE id = ?');
  $stmt->execute([(int)$_GET['delete']]);
  header('Location: admin_catalog.php'); exit;
}

// Значения формы и ошибки (используются и для отображения после ошибки)
$edit = ['id' => 0, 'title' => '', 'description' => '', 'duration' => 0, 'price' => 0, 'image' => ''];
$errors = [];

// Создание / обновление
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $edit['id'] = (int)($_POST['id'] ?? 0);
  $edit['title'] = trim($_POST['title'] ?? '');
  $edit['description'] = trim($_POST['description'] ?? '');
  $edit['duration'] = (int)($_POST['duration'] ?? 0);
  $edit['price'] = (int)($_POST['price'] ?? 0);
  $edit['image'] = trim($_POST['image_current'] ?? 'image01.webp');

  // Валидация
  if ($edit['title'] === '') {
    $errors['title'] = 'Введите название';
  }
  if ($edit['duration'] < 0 || $edit['duration'] > 9999) {
    $errors['duration'] = 'Длительность от 0 до 9999 часов';
  }
  if ($edit['price'] < 0 || $edit['price'] > 9999999) {
    $errors['price'] = 'Цена от 0 до 9 999 999 ₽';
  }

  // Если загрузили новый файл — переносим в img/
  if (!$errors && !empty($_FILES['image_file']['tmp_name'])) {
    $ext = strtolower(pathinfo($_FILES['image_file']['name'], PATHINFO_EXTENSION));
    if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
      $edit['image'] = 'upload_' . time() . '.' . $ext;
      move_uploaded_file($_FILES['image_file']['tmp_name'], __DIR__ . '/img/' . $edit['image']);
    }
  }

  // Запись в БД
  if (!$errors) {
    if ($edit['id'] > 0) {
      $stmt = $pdo->prepare('UPDATE catalog SET title=?, description=?, duration=?, price=?, image=? WHERE id=?');
      $stmt->execute([$edit['title'], $edit['description'], $edit['duration'], $edit['price'], $edit['image'], $edit['id']]);
    } else {
      $stmt = $pdo->prepare('INSERT INTO catalog (title, description, duration, price, image) VALUES (?, ?, ?, ?, ?)');
      $stmt->execute([$edit['title'], $edit['description'], $edit['duration'], $edit['price'], $edit['image']]);
    }
    header('Location: admin_catalog.php'); exit;
  }
}

// Редактирование — загружаем строку (если ошибок POST не было)
if (!$errors && ($_GET['edit'] ?? 0) > 0) {
  $stmt = $pdo->prepare('SELECT * FROM catalog WHERE id = ?');
  $stmt->execute([(int)$_GET['edit']]);
  $edit = $stmt->fetch() ?: $edit;
}

$catalog = $pdo->query('SELECT * FROM catalog ORDER BY id')->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Каталог — управление</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'header.php'; ?>

<main class="container">
  <?php include 'admin_tabs.php'; ?>
  <h1>Каталог курсов</h1>

  <table class="tbl">
    <tr><th>Название</th><th>Часы</th><th>Цена</th><th>Картинка</th><th></th></tr>
    <?php foreach ($catalog as $course): ?>
      <tr>
        <td><?= htmlspecialchars($course['title']) ?></td>
        <td><?= (int)$course['duration'] ?></td>
        <td><?= (int)$course['price'] ?> ₽</td>
        <td><img src="img/<?= htmlspecialchars($course['image']) ?>" class="thumb" alt=""></td>
        <td>
          <a href="?edit=<?= (int)$course['id'] ?>">✎</a>
          <a href="?delete=<?= (int)$course['id'] ?>" class="del" onclick="return confirm('Удалить курс?')">✕</a>
        </td>
      </tr>
    <?php endforeach; ?>
  </table>

  <h2><?= $edit['id'] ? 'Редактировать курс' : 'Добавить курс' ?></h2>
  <form method="post" class="form" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?= (int)$edit['id'] ?>">
    <input type="hidden" name="image_current" value="<?= htmlspecialchars($edit['image']) ?>">

    <label>Название
      <input name="title" required value="<?= htmlspecialchars($edit['title']) ?>">
    </label>
    <span class="err"><?= $errors['title'] ?? '' ?></span>

    <label>Описание
      <textarea name="description"><?= htmlspecialchars($edit['description']) ?></textarea>
    </label>

    <label>Длительность (часов)
      <input type="number" name="duration" min="0" max="9999" value="<?= (int)$edit['duration'] ?>">
    </label>
    <span class="err"><?= $errors['duration'] ?? '' ?></span>

    <label>Цена (₽)
      <input type="number" name="price" min="0" max="9999999" value="<?= (int)$edit['price'] ?>">
    </label>
    <span class="err"><?= $errors['price'] ?? '' ?></span>

    <label>Картинка (JPG / PNG / WEBP)
      <input type="file" name="image_file" accept="image/jpeg,image/png,image/webp">
    </label>
    <?php if ($edit['image']): ?>
      <p>Текущая: <img src="img/<?= htmlspecialchars($edit['image']) ?>" class="thumb" alt=""></p>
    <?php endif; ?>

    <button><?= $edit['id'] ? 'Сохранить' : 'Добавить' ?></button>
    <?php if ($edit['id']): ?>
      <a href="admin_catalog.php">Отмена</a>
    <?php endif; ?>
  </form>
</main>

<?php include 'footer.php'; ?>
</body>
</html>
