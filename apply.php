<?php
require 'db.php';
if (!user()) { header('Location: login.php'); exit; }
if (admin()) { header('Location: admin.php'); exit; }

$courses = [
  'Основы алгоритмизации и программирования',
  'Основы веб-дизайна',
  'Основы проектирования баз данных'
];

$course = '';
$startDate = '';
$payment = '';
$errors = [];

$minDate = date('Y-m-d');
$maxDate = date('Y-m-d', strtotime('+1 year'));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $course = $_POST['course'] ?? '';
  $startDate = $_POST['start_date'] ?? '';
  $payment = $_POST['payment'] ?? '';

  if (!in_array($course, $courses)) {
    $errors['course'] = 'Выберите курс из списка';
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
    // Конвертация YYYY-MM-DD из календаря → ДД.ММ.ГГГГ по ТЗ
    [$year, $month, $day] = explode('-', $startDate);
    $dateForDb = "$day.$month.$year";

    $stmt = $pdo->prepare('
      INSERT INTO orders (user_id, course, start_date, payment, status)
      VALUES (?, ?, ?, ?, ?)');
    $stmt->execute([user()['id'], $course, $dateForDb, $payment, 'Новая']);
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
      <select name="course" required>
        <option value="" disabled <?= $course === '' ? 'selected' : '' ?>>— выберите курс —</option>
        <?php foreach ($courses as $courseOption): ?>
          <option <?= $course === $courseOption ? 'selected' : '' ?>>
            <?= $courseOption ?>
          </option>
        <?php endforeach; ?>
      </select>
    </label>
    <span class="err"><?= $errors['course'] ?? '' ?></span>

    <label>Дата начала обучения
      <input type="date" name="start_date"
             min="<?= $minDate ?>" max="<?= $maxDate ?>"
             value="<?= htmlspecialchars($startDate) ?>" required>
    </label>
    <span class="err"><?= $errors['start_date'] ?? '' ?></span>

    <label>Способ оплаты
      <select name="payment" required>
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
