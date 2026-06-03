<?php
session_start();

$pdo = new PDO(
  'mysql:host=localhost;port=8889;dbname=korochki;charset=utf8mb4',
  'root',
  'root',
  [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  ]
);

// Текущий авторизованный пользователь (или null)
function user() {
  return $_SESSION['user'] ?? null;
}

// Является ли текущий пользователь админом
function admin() {
  return user() && user()['role'] === 'admin';
}
