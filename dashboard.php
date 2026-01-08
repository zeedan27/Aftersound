<?php
// speak-chat/dashboard.php
require_once __DIR__ . '/config/db.php';

// Protect page
requireLogin();

$username = currentUsername();
$success = flash_get('success');
$error   = flash_get('error');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard - Speak Chat</title>
  <link rel="stylesheet" href="assets/css/style.css" />
</head>
<body>
  <div class="dashboard-page">
    <!-- Header -->
    <header class="dashboard-header">
      <h1>Welcome, <?= e($username ?? '') ?> 👋</h1>

      <a class="logout-btn" href="logout.php">Logout</a>
    </header>

    <?php if ($success): ?>
      <div class="alert success"><?= e($success) ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
      <div class="alert error"><?= e($error) ?></div>
    <?php endif; ?>

    <!-- SECTION 1: Friend Requests (placeholder) -->
    <section class="card">
      <h2>Friend Requests</h2>
      <p class="muted">No requests yet.</p>
    </section>

    <!-- SECTION 2: Add Friend -->
    <section class="card">
      <h2>Add Friend</h2>

      <form method="POST" action="add-friend.php" class="add-friend-form">
        <input
          type="text"
          name="username"
          placeholder="Enter username"
          required
        />
        <button type="submit">Add</button>
      </form>
    </section>

    <!-- SECTION 3: Friends List (placeholder) -->
    <section class="card">
      <h2>Your Friends</h2>
      <p class="muted">You have no friends yet.</p>

      <!-- Example (future):
      <div class="friend-item">
        <span>friend_username</span>
        <div class="friend-actions">
          <a href="chat.php?user=2&theme=T1">🎨 Color & Sound</a>
          <a href="chat.php?user=2&theme=T3">✏️ Symbol Draw</a>
          <a href="chat.php?user=2&theme=T8">💡 Light & Sound</a>
        </div>
      </div>
      -->
    </section>
  </div>
</body>
</html>
