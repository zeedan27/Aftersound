<?php
// PAGE 4: Chat interface (theme-based) (placeholder)
session_start();
require_once __DIR__ . '/config/db.php';

// Expected query: ?user=ID&theme=1|3|8
$peer = isset($_GET['user']) ? intval($_GET['user']) : 0;
theme = isset($_GET['theme']) ? intval($_GET['theme']) : 1;
?><!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Speak Chat — Chat</title>
  <link rel="stylesheet" href="assets/css/style.css">
  <script src="assets/js/t1-chat.js" defer></script>
  <script src="assets/js/t3-chat.js" defer></script>
  <script src="assets/js/t8-chat.js" defer></script>
</head>
<body>
  <div class="container">
    <h1>Chat</h1>
    <div id="chat-area">Chat UI will load here based on theme.</div>
  </div>
</body>
</html>
