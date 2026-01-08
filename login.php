<?php
// speak-chat/login.php
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
  <title>AfterSound - Log In</title>

  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Lilita+One&display=swap" rel="stylesheet" />

  <link rel="stylesheet" href="assets/css/style.css" />
</head>
<body>
  <div class="aftersound-login">
    <div class="asw-waves asw-waves-left" aria-hidden="true"></div>
    <div class="asw-waves asw-waves-right" aria-hidden="true"></div>

    <main class="as-center">
      <div class="as-shell">
        <div class="as-head as-pop-1">
          <div class="as-badge">LOG IN</div>
          <h1 class="as-title">AFTERSOUND</h1>
          <p class="as-sub">Welcome back - continue the conversation.</p>
          <div class="as-under-line" aria-hidden="true"></div>
        </div>

        <section class="as-card as-pop-2" aria-label="Login form">
          <?php if ($success): ?>
            <div class="as-alert as-alert-success"><?= e($success) ?></div>
          <?php endif; ?>

          <?php if ($error): ?>
            <div class="as-alert as-alert-error"><?= e($error) ?></div>
          <?php endif; ?>

          <form class="as-form" method="post" autocomplete="off">
            <div class="as-row">
              <label class="as-label" for="identifier">Email</label>
              <input class="as-input" id="identifier" name="identifier" type="text" placeholder="you@example.com" required />
            </div>

            <div class="as-row">
              <label class="as-label" for="password">Password</label>
              <input class="as-input" id="password" name="password" type="password" placeholder="Enter your password" required />
            </div>

            <div class="as-meta">
              <label class="as-check">
                <input type="checkbox" />
                <span>Remember me</span>
              </label>

              <a class="as-link as-link-small" href="#">Forgot password?</a>
            </div>

            <button class="as-btn as-pop-3" type="submit">
              <span class="as-btn-inner">log in</span>
            </button>

            <div class="as-alt as-pop-4">
              <span>New here?</span>
              <a class="as-link" href="register.php">Create an account</a>
            </div>
          </form>
        </section>

        <div class="as-foot as-pop-4">
          <span class="as-dot" aria-hidden="true"></span>
          <span>Tip: Your mood can speak before your words.</span>
        </div>
      </div>
    </main>
  </div>
</body>
</html>
