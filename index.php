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
  <title>AfterSound</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Lilita+One&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="assets/css/style.css" />
</head>
<body>
  <div class="aftersound-welcome">
    <div class="asw-waves asw-waves-left" aria-hidden="true"></div>
    <div class="asw-waves asw-waves-right" aria-hidden="true"></div>

    <main class="asw-center">
      <div class="asw-stack">
        <div class="asw-title-wrap asw-pop-1">
          <div class="asw-title">WELCOME TO</div>
        </div>

        <div class="asw-brand-row asw-pop-2">
          <div class="asw-brand-pill">
            <span class="asw-brand-text">AFTERSOUND</span>
          </div>

          <div class="asw-burst" aria-hidden="true">
            <span class="asw-burst-line l1"></span>
            <span class="asw-burst-line l2"></span>
            <span class="asw-burst-line l3"></span>
            <span class="asw-burst-line l4"></span>
            <span class="asw-burst-line l5"></span>
          </div>
        </div>

        <div class="asw-sub asw-pop-3">
          WHERE CONVERSATIONS GO BEYOND JUST WORDS.
        </div>

        <div class="asw-under-line asw-pop-3" aria-hidden="true"></div>

        <div class="asw-actions asw-pop-4">
          <a class="asw-btn" href="register.php">
            <span class="asw-btn-inner">sign up</span>
          </a>
          <a class="asw-btn asw-btn-secondary" href="login.php">
            <span class="asw-btn-inner">log in</span>
          </a>
        </div>
      </div>
    </main>
  </div>
</body>
</html>
