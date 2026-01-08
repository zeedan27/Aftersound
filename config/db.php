<?php
// speak-chat/config/db.php

declare(strict_types=1);

// Always start session first (needed for login/logout/auth checks)
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

/**
 * -----------------------------
 * DATABASE CONFIG
 * -----------------------------
 * Option A (recommended): set env vars in server/.env
 *   DB_HOST, DB_NAME, DB_USER, DB_PASS
 *
 * Option B: edit defaults below for local XAMPP/WAMP testing.
 */
const DB_HOST = 'localhost';
const DB_NAME = 'speak_chat';
const DB_USER = 'root';
const DB_PASS = ''; // XAMPP default is empty

/**
 * -----------------------------
 * PDO CONNECTION (Singleton)
 * -----------------------------
 */
function db(): PDO {
  static $pdo = null;
  if ($pdo instanceof PDO) return $pdo;

  $host = getenv('DB_HOST') ?: DB_HOST;
  $name = getenv('DB_NAME') ?: DB_NAME;
  $user = getenv('DB_USER') ?: DB_USER;
  $pass = getenv('DB_PASS') ?: DB_PASS;

  $dsn = "mysql:host={$host};dbname={$name};charset=utf8mb4";

  $options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
  ];

  $pdo = new PDO($dsn, $user, $pass, $options);
  return $pdo;
}

/**
 * -----------------------------
 * BASIC HELPERS
 * -----------------------------
 */
function e(string $str): string {
  return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

function redirect(string $path): void {
  header("Location: {$path}");
  exit;
}

function isPost(): bool {
  return ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST';
}

function getPost(string $key, string $default = ''): string {
  return isset($_POST[$key]) ? trim((string)$_POST[$key]) : $default;
}

/**
 * Flash messages (success/error)
 * Usage:
 *   flash_set('success', 'Done!');
 *   $msg = flash_get('success');
 */
function flash_set(string $key, string $message): void {
  $_SESSION['flash'][$key] = $message;
}

function flash_get(string $key): ?string {
  if (!isset($_SESSION['flash'][$key])) return null;
  $msg = (string)$_SESSION['flash'][$key];
  unset($_SESSION['flash'][$key]);
  return $msg;
}

/**
 * -----------------------------
 * AUTH HELPERS
 * -----------------------------
 * Convention: store logged-in user in session like:
 *   $_SESSION['user'] = ['id' => 1, 'username' => 'abc', 'email' => '...'];
 */
function isLoggedIn(): bool {
  return isset($_SESSION['user']) && isset($_SESSION['user']['id']);
}

function requireLogin(string $redirectTo = '/index.php'): void {
  if (!isLoggedIn()) {
    flash_set('error', 'Please log in first.');
    redirect($redirectTo);
  }
}

function currentUserId(): ?int {
  if (!isLoggedIn()) return null;
  return (int)$_SESSION['user']['id'];
}

function currentUsername(): ?string {
  if (!isLoggedIn()) return null;
  return (string)$_SESSION['user']['username'];
}

/**
 * -----------------------------
 * USER LOOKUPS
 * -----------------------------
 * Expected "users" table (minimum):
 *   id (INT PK AI)
 *   username (VARCHAR UNIQUE)
 *   email (VARCHAR UNIQUE)
 *   password_hash (VARCHAR)
 *   created_at (DATETIME)
 */
function findUserByUsername(string $username): ?array {
  $sql = "SELECT id, username, email, password_hash FROM users WHERE username = ? LIMIT 1";
  $stmt = db()->prepare($sql);
  $stmt->execute([$username]);
  $row = $stmt->fetch();
  return $row ?: null;
}

function findUserByEmail(string $email): ?array {
  $sql = "SELECT id, username, email, password_hash FROM users WHERE email = ? LIMIT 1";
  $stmt = db()->prepare($sql);
  $stmt->execute([$email]);
  $row = $stmt->fetch();
  return $row ?: null;
}

function findUserById(int $id): ?array {
  $sql = "SELECT id, username, email FROM users WHERE id = ? LIMIT 1";
  $stmt = db()->prepare($sql);
  $stmt->execute([$id]);
  $row = $stmt->fetch();
  return $row ?: null;
}

/**
 * -----------------------------
 * FRIENDSHIP HELPERS
 * -----------------------------
 * Expected "friend_requests" table (minimum):
 *   id (INT PK AI)
 *   from_user_id (INT)
 *   to_user_id (INT)
 *   status (ENUM 'pending','accepted','rejected')
 *   created_at (DATETIME)
 */
function areFriends(int $userA, int $userB): bool {
  // You can model accepted friendships in friend_requests(status='accepted')
  // or a separate friends table. This helper assumes friend_requests.
  $sql = "
    SELECT 1
    FROM friend_requests
    WHERE status = 'accepted'
      AND (
        (from_user_id = ? AND to_user_id = ?)
        OR
        (from_user_id = ? AND to_user_id = ?)
      )
    LIMIT 1
  ";
  $stmt = db()->prepare($sql);
  $stmt->execute([$userA, $userB, $userB, $userA]);
  return (bool)$stmt->fetchColumn();
}

function pendingRequestExists(int $from, int $to): bool {
  $sql = "
    SELECT 1
    FROM friend_requests
    WHERE status = 'pending' AND from_user_id = ? AND to_user_id = ?
    LIMIT 1
  ";
  $stmt = db()->prepare($sql);
  $stmt->execute([$from, $to]);
  return (bool)$stmt->fetchColumn();
}
