<?php
// Backend: Friend request actions
require_once __DIR__ . '/config/db.php';

requireLogin();

if (!isPost()) {
  http_response_code(405);
  exit;
}

$userId = currentUserId();
$requestId = (int)($_POST['request_id'] ?? 0);
$action = (string)($_POST['action'] ?? '');
$returnQuery = trim((string)($_POST['q'] ?? ''));
$redirectTo = 'dashboard.php' . ($returnQuery !== '' ? ('?q=' . urlencode($returnQuery)) : '');

if (!$userId || $requestId <= 0) {
  flash_set('error', 'Invalid request.');
  redirect($redirectTo);
}

$stmt = db()->prepare("
  SELECT id, from_user_id, to_user_id, status
  FROM friend_requests
  WHERE id = ? AND to_user_id = ?
  LIMIT 1
");
$stmt->execute([$requestId, $userId]);
$request = $stmt->fetch();

if (!$request || (string)$request['status'] !== 'pending') {
  flash_set('error', 'Request not found.');
  redirect($redirectTo);
}

if ($action === 'accept') {
  ensureFriendsTable();
  $db = db();
  $db->beginTransaction();
  try {
    $stmt = $db->prepare("DELETE FROM friend_requests WHERE id = ?");
    $stmt->execute([$requestId]);

    $stmt = $db->prepare("
      INSERT IGNORE INTO friends (user_id, friend_id) VALUES (?, ?)
    ");
    $stmt->execute([(int)$request['from_user_id'], (int)$request['to_user_id']]);
    $stmt->execute([(int)$request['to_user_id'], (int)$request['from_user_id']]);

    $db->commit();
    flash_set('success', 'Friend request accepted.');
  } catch (PDOException $e) {
    $db->rollBack();
    flash_set('error', 'Could not accept friend request.');
  }
} elseif ($action === 'deny') {
  $stmt = db()->prepare("DELETE FROM friend_requests WHERE id = ?");
  $stmt->execute([$requestId]);
  flash_set('success', 'Friend request declined.');
} else {
  flash_set('error', 'Invalid action.');
}

redirect($redirectTo);
