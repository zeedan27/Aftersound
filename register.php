<?php
// PAGE 2: Registration page (placeholder)
session_start();
require_once __DIR__ . '/config/db.php';

if (isset($_POST['register'])) {
    // TODO: implement registration handling
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
