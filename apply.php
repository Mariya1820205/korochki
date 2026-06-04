<?php
require 'db.php';
if (!user()) { header('Location: login.php'); exit; }
if (admin()) { header('Location: admin.php'); exit; }

// Все курсы из БД
$catalog = $pdo->query('SELECT id, title FROM catalog ORDER BY title')->fetchAll();
$catalogIds = array_column($catalog, 'id');

$minDate = date('Y-m-d');
$maxDate = date('Y-m-d', strtotime('+1 year'));

// Предвыбор курса из ссылки "Записаться" на каталоге (?course=ID)
$catalogId = (int)($_GET['course'] ?? 0);
$startDate = '';
$payment = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $catalogId = (int)($_POST['catalog_id'] ?? 0);
  $startDate = trim($_POST['start_date'] ?? '');
  $payment = $_POST['payment'] ?? '';

  if (!in_array($catalogId, $catalogIds)) {
    $errors['catalog_id'] = 'Выберите курс из списка';
  }
  if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $startDate)) {
    $errors['start_date'] = 'Выберите дату';
  } elseif ($startDate < $minDate || $startDate > $maxDate) {
    $errors['start_date'] = 'Дата должна быть от сегодня до года вперёд';
  }
  if (!in_array($payment, ['Наличные', 'Перевод'])) {
    $errors['payment'] = 'Выберите способ оплаты';
  }

  if (!$errors) {
    $stmt = $pdo->prepare('
      INSERT INTO orders (user_id, catalog_id, start_date, payment)
      VALUES (?, ?, ?, ?)');
    $stmt->execute([user()['id'], $catalogId, $startDate, $payment]);
    header('Location: orders.php'); exit;
  }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Подать заявку</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'header.php'; ?>

<main class="container">
  <h1>Подать заявку</h1>
  <form method="post" class="form">
    <label>Курс
      <select name="catalog_id">
        <option value="" disabled <?= $catalogId === 0 ? 'selected' : '' ?>>— выберите курс —</option>
        <?php foreach ($catalog as $course): ?>
          <option value="<?= (int)$course['id'] ?>" <?= $catalogId === (int)$course['id'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($course['title']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </label>
    <span class="err"><?= $errors['catalog_id'] ?? '' ?></span>

    <label>Дата начала обучения
      <input type="date" name="start_date"
             min="<?= $minDate ?>" max="<?= $maxDate ?>"
             value="<?= htmlspecialchars($startDate) ?>" required>
    </label>
    <span class="err"><?= $errors['start_date'] ?? '' ?></span>

    <label>Способ оплаты
      <select name="payment">
        <option value="" disabled <?= $payment === '' ? 'selected' : '' ?>>— выберите способ —</option>
        <option <?= $payment === 'Наличные' ? 'selected' : '' ?>>Наличные</option>
        <option <?= $payment === 'Перевод' ? 'selected' : '' ?>>Перевод</option>
      </select>
    </label>
    <span class="err"><?= $errors['payment'] ?? '' ?></span>

    <button>Отправить</button>
  </form>
</main>

<?php include 'footer.php'; ?>
</body>
</html>
