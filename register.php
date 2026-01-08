<?php
// PAGE 2: Registration page (placeholder)
session_start();
require_once __DIR__ . '/config/db.php';

$error = flash_get('error');
$success = flash_get('success');

if (isPost() && isset($_POST['register'])) {
    $name = getPost('name');
    $email = getPost('email');
    $password = getPost('password');

    if ($name === '' || $email === '' || $password === '') {
        $error = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } elseif (findUserByUsername($name)) {
        $error = 'That username is already taken.';
    } elseif (findUserByEmail($email)) {
        $error = 'That email is already registered.';
    } else {
        try {
            $sql = "INSERT INTO users (username, email, password_hash, created_at) VALUES (?, ?, ?, NOW())";
            $stmt = db()->prepare($sql);
            $stmt->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT)]);

            flash_set('success', 'Registration successful. Please log in.');
            redirect('index.php');
        } catch (PDOException $e) {
            $error = 'Registration failed. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>AfterSound — Sign Up</title>

  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Lilita+One&display=swap" rel="stylesheet" />

  <link rel="stylesheet" href="assets/css/style.css" />
</head>

<body>
  <div class="aftersound-signup">
    <div class="asw-waves asw-waves-left" aria-hidden="true"></div>
    <div class="asw-waves asw-waves-right" aria-hidden="true"></div>

    <main class="as-center">
      <div class="as-shell">
        <div class="as-head as-pop-1">
          <div class="as-badge">JOIN</div>
          <h1 class="as-title">AFTERSOUND</h1>
          <p class="as-sub">Create your account to chat beyond words.</p>
          <div class="as-under-line" aria-hidden="true"></div>
        </div>

        <section class="as-card as-pop-2" aria-label="Sign up form">
          <?php if ($success): ?>
            <div class="as-alert as-alert-success"><?= e($success) ?></div>
          <?php endif; ?>

          <?php if ($error): ?>
            <div class="as-alert as-alert-error"><?= e($error) ?></div>
          <?php endif; ?>

          <form class="as-form" method="post" autocomplete="off">
            <div class="as-row">
              <label class="as-label" for="name">Full Name</label>
              <input class="as-input" id="name" name="name" type="text" placeholder="Your name" required />
            </div>

            <div class="as-row">
              <label class="as-label" for="email">Email</label>
              <input class="as-input" id="email" name="email" type="email" placeholder="you@example.com" required />
            </div>

            <div class="as-row">
              <label class="as-label" for="password">Password</label>
              <input class="as-input" id="password" name="password" type="password" placeholder="Create a password" required />
              <div class="as-hint">Use at least 8 characters.</div>
            </div>

            <div class="as-row">
              <label class="as-label" for="confirm">Confirm Password</label>
              <input class="as-input" id="confirm" name="confirm" type="password" placeholder="Repeat password" required />
            </div>

            <button class="as-btn as-pop-3" type="submit" name="register">
              <span class="as-btn-inner">sign up</span>
            </button>

            <div class="as-alt as-pop-4">
              <span>Already have an account?</span>
              <a class="as-link" href="login.php">Log in</a>
            </div>
          </form>
        </section>

        <div class="as-foot as-pop-4">
          <span class="as-dot" aria-hidden="true"></span>
          <span>Tip: Your first message can be a color, not a word.</span>
        </div>
      </div>
    </main>
  </div>
</body>
</html>