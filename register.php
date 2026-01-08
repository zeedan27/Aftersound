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
?><!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Speak Chat — Register</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
  <div class="container">
    <h1>Register</h1>
    <?php if ($success): ?>
      <div class="alert success"><?= e($success) ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
      <div class="alert error"><?= e($error) ?></div>
    <?php endif; ?>
    <form method="post">
      <label>Name<br><input type="text" name="name"></label><br>
      <label>Email<br><input type="email" name="email"></label><br>
      <label>Password<br><input type="password" name="password"></label><br>
      <button type="submit" name="register">Register</button>
    </form>
    <p><a href="index.php">Already have an account? Login</a></p>
  </div>
</body>
</html>
