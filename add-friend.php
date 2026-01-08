<?php
// Backend: Add friend handler
require_once __DIR__ . '/config/db.php';

requireLogin();

if (!isPost()) {
  http_response_code(405);
  exit;
}

$userId = currentUserId();
$friendId = (int)($_POST['friend_id'] ?? 0);
$returnQuery = trim((string)($_POST['q'] ?? ''));
$redirectTo = 'dashboard.php' . ($returnQuery !== '' ? ('?q=' . urlencode($returnQuery)) : '');

if (!$userId || $friendId <= 0 || $friendId === $userId) {
  flash_set('error', 'Invalid user.');
  redirect($redirectTo);
}

if (!findUserById($friendId)) {
  flash_set('error', 'User not found.');
  redirect($redirectTo);
}

if (areFriends($userId, $friendId)) {
  flash_set('success', 'You are already friends.');
  redirect($redirectTo);
}

if (pendingRequestExists($userId, $friendId)) {
  flash_set('success', 'Friend request already sent.');
  redirect($redirectTo);
}

if (pendingRequestExists($friendId, $userId)) {
  flash_set('success', 'This user already sent you a request.');
  redirect($redirectTo);
}

try {
  $sql = "INSERT INTO friend_requests (from_user_id, to_user_id, status) VALUES (?, ?, 'pending')";
  $stmt = db()->prepare($sql);
  $stmt->execute([$userId, $friendId]);
  flash_set('success', 'Friend request sent.');
} catch (PDOException $e) {
  flash_set('error', 'Could not send friend request.');
}

redirect($redirectTo);
