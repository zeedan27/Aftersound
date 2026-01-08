<?php
// speak-chat/index.php
require_once __DIR__ . '/config/db.php';

// If already logged in, go to dashboard
if (isLoggedIn()) {
  redirect('dashboard.php');
}

$error = flash_get('error');
$success = flash_get('success');

if (isPost()) {
  $identifier = getPost('identifier'); // username OR email
  $password   = getPost('password');

  if ($identifier === '' || $password === '') {
    $error = "Please enter your username/email and password.";
  } else {
    // Try username first, then email
    $user = findUserByUsername($identifier);
    if (!$user) $user = findUserByEmail($identifier);

    if (!$user || !password_verify($password, (string)$user['password_hash'])) {
      $error = "Invalid credentials.";
    } else {
      // Set session user (keep it minimal & consistent)
      $_SESSION['user'] = [
        'id' => (int)$user['id'],
        'username' => (string)$user['username'],
        'email' => (string)$user['email'],
      ];

      redirect('dashboard.php');
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login - Speak Chat</title>
  <link rel="stylesheet" href="assets/css/style.css" />
</head>
<body>
  <div class="auth-page login-page">
    <div class="auth-card">
      <h1>Login</h1>

      <?php if ($success): ?>
        <div class="alert success"><?= e($success) ?></div>
      <?php endif; ?>

      <?php if ($error): ?>
        <div class="alert error"><?= e($error) ?></div>
      <?php endif; ?>

      <form method="POST" autocomplete="off">
        <label>
          Username or Email
          <input type="text" name="identifier" placeholder="e.g. zeedan or zeedan@mail.com" required />
        </label>

        <label>
          Password
          <input type="password" name="password" placeholder="••••••••" required />
        </label>

        <button type="submit">Login</button>
      </form>

      <p class="auth-link">
        Don’t have an account? <a href="register.php">Register</a>
      </p>
    </div>
  </div>
</body>
</html>
