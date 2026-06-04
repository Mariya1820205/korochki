<?php
session_start();

$pdo = new PDO(
  'mysql:host=localhost;port=8889;dbname=korochki;charset=utf8mb4',
  'root',
  'root',
  [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4",
  ]
);

function user() {
  return $_SESSION['user'] ?? null;
}

function admin() {
  return user() && user()['role'] === 'admin';
}

function formatDate($date) {
  if (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $date, $m)) {
    return "$m[3].$m[2].$m[1]";
  }
  return $date;
}
