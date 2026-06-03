<?php
require 'db.php';
if (!user()) { header('Location: login.php'); exit; }
if (admin()) { header('Location: admin.php'); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $reviewText = trim($_POST['review'] ?? '');
  $orderId = (int)($_POST['id'] ?? 0);

  if ($reviewText !== '' && $orderId > 0) {
    $stmt = $pdo->prepare('
      UPDATE orders
      SET review = ?
      WHERE id = ? AND user_id = ? AND status = "Обучение завершено"');
    $stmt->execute([$reviewText, $orderId, user()['id']]);
  }
  header('Location: orders.php'); exit;
}

$stmt = $pdo->prepare('SELECT * FROM orders WHERE user_id = ? ORDER BY id DESC');
$stmt->execute([user()['id']]);
$orders = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Мои заявки</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'header.php'; ?>

<main class="container">
  <h1>Мои заявки</h1>

  <?php if (!$orders): ?>
    <p>У вас пока нет заявок. <a href="apply.php">Подать заявку</a></p>
  <?php endif; ?>

  <?php foreach ($orders as $order): ?>
    <div class="card">
      <h3><?= htmlspecialchars($order['course']) ?></h3>
      <p>Дата начала: <?= htmlspecialchars($order['start_date']) ?></p>
      <p>Способ оплаты: <?= htmlspecialchars($order['payment']) ?></p>
      <p>Статус: <b><?= htmlspecialchars($order['status']) ?></b></p>

      <?php if ($order['status'] === 'Обучение завершено'): ?>
        <?php if ($order['review']): ?>
          <p>Ваш отзыв: <i><?= htmlspecialchars($order['review']) ?></i></p>
        <?php else: ?>
          <form method="post" class="form">
            <input type="hidden" name="id" value="<?= $order['id'] ?>">
            <label>Отзыв
              <textarea name="review" required></textarea>
            </label>
            <button>Оставить отзыв</button>
          </form>
        <?php endif; ?>
      <?php endif; ?>
    </div>
  <?php endforeach; ?>
</main>

<?php include 'footer.php'; ?>
</body>
</html>
