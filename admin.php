<?php
require 'db.php';
if (!admin()) { header('Location: login.php'); exit; }

$statuses = ['Новая', 'Идет обучение', 'Обучение завершено'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $newStatus = $_POST['status'] ?? '';
  $orderId = (int)($_POST['id'] ?? 0);

  if (in_array($newStatus, $statuses) && $orderId > 0) {
    $stmt = $pdo->prepare('UPDATE orders SET status = ? WHERE id = ?');
    $stmt->execute([$newStatus, $orderId]);
    header('Location: admin.php?success=1'); exit;
  }
}

$message = isset($_GET['success']) ? 'Статус заявки изменён' : '';

$filter = $_GET['status'] ?? '';
if ($filter !== '' && !in_array($filter, $statuses)) $filter = '';

$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 5;
$offset = ($page - 1) * $perPage;

if ($filter !== '') {
  $totalStmt = $pdo->prepare('SELECT COUNT(*) FROM orders WHERE status = ?');
  $totalStmt->execute([$filter]);
  $total = $totalStmt->fetchColumn();

  $listStmt = $pdo->prepare('
    SELECT orders.*, users.fio, users.login
    FROM orders
    JOIN users ON users.id = orders.user_id
    WHERE orders.status = ?
    ORDER BY orders.id DESC
    LIMIT ' . $perPage . ' OFFSET ' . $offset);
  $listStmt->execute([$filter]);
} else {
  $total = $pdo->query('SELECT COUNT(*) FROM orders')->fetchColumn();

  $listStmt = $pdo->query('
    SELECT orders.*, users.fio, users.login
    FROM orders
    JOIN users ON users.id = orders.user_id
    ORDER BY orders.id DESC
    LIMIT ' . $perPage . ' OFFSET ' . $offset);
}
$orders = $listStmt->fetchAll();
$pages = max(1, ceil($total / $perPage));
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Админ-панель</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'header.php'; ?>

<main class="container">
  <h1>Заявки пользователей</h1>

  <?php if ($message): ?>
    <div class="toast"><?= $message ?></div>
  <?php endif; ?>

  <form method="get" class="filter">
    <label>Фильтр по статусу:
      <select name="status" onchange="this.form.submit()">
        <option value="">Все</option>
        <?php foreach ($statuses as $status): ?>
          <option <?= $filter === $status ? 'selected' : '' ?>><?= $status ?></option>
        <?php endforeach; ?>
      </select>
    </label>
  </form>

  <table class="tbl">
    <tr>
      <th>Пользователь</th>
      <th>Курс</th>
      <th>Дата</th>
      <th>Оплата</th>
      <th>Статус</th>
    </tr>
    <?php foreach ($orders as $order): ?>
      <tr>
        <td><?= htmlspecialchars($order['fio']) ?> (<?= htmlspecialchars($order['login']) ?>)</td>
        <td><?= htmlspecialchars($order['course']) ?></td>
        <td><?= htmlspecialchars($order['start_date']) ?></td>
        <td><?= htmlspecialchars($order['payment']) ?></td>
        <td>
          <form method="post">
            <input type="hidden" name="id" value="<?= $order['id'] ?>">
            <select name="status" onchange="this.form.submit()">
              <?php foreach ($statuses as $status): ?>
                <option <?= $order['status'] === $status ? 'selected' : '' ?>><?= $status ?></option>
              <?php endforeach; ?>
            </select>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
  </table>

  <div class="pages">
    <?php for ($i = 1; $i <= $pages; $i++): ?>
      <a href="?status=<?= urlencode($filter) ?>&page=<?= $i ?>"
         class="<?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
    <?php endfor; ?>
  </div>
</main>

<?php include 'footer.php'; ?>
</body>
</html>
