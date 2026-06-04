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

// CSS-классы для бейджей статусов
$badgeClass = [
  'Новая' => 'badge-new',
  'Идет обучение' => 'badge-progress',
  'Обучение завершено' => 'badge-done',
];
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
    <div class="empty">
      <p>У вас пока нет заявок.</p>
      <a href="catalog.php" class="btn">Смотреть каталог</a>
    </div>
  <?php endif; ?>

  <div class="orders">
    <?php foreach ($orders as $order): ?>
      <article class="order order--<?= $badgeClass[$order['status']] ?? 'badge-new' ?>">
        <header class="order-head">
          <h3><?= htmlspecialchars($order['course']) ?></h3>
          <span class="badge <?= $badgeClass[$order['status']] ?? 'badge-new' ?>">
            <?= htmlspecialchars($order['status']) ?>
          </span>
        </header>

        <div class="order-info">
          <div><span>Дата начала</span><b><?= formatDate($order['start_date']) ?></b></div>
          <div><span>Способ оплаты</span><b><?= htmlspecialchars($order['payment']) ?></b></div>
        </div>

        <?php if ($order['status'] === 'Обучение завершено'): ?>
          <?php if ($order['review']): ?>
            <div class="review">
              <span>Ваш отзыв:</span>
              <p><?= htmlspecialchars($order['review']) ?></p>
            </div>
          <?php else: ?>
            <form method="post" class="form review-form">
              <input type="hidden" name="id" value="<?= (int)$order['id'] ?>">
              <label>Оставьте отзыв о курсе
                <textarea name="review" required placeholder="Расскажите, что понравилось..."></textarea>
              </label>
              <button>Отправить отзыв</button>
            </form>
          <?php endif; ?>
        <?php endif; ?>
      </article>
    <?php endforeach; ?>
  </div>
</main>

<?php include 'footer.php'; ?>
</body>
</html>
