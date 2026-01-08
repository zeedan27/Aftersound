<?php
// speak-chat/chat.php
require_once __DIR__ . '/config/db.php';

requireLogin();

$userId = currentUserId();
$peerId = isset($_GET['user']) ? (int)$_GET['user'] : 0;
if (!$userId || $peerId <= 0 || $peerId === $userId) {
  flash_set('error', 'Invalid chat.');
  redirect('dashboard.php');
}

$peer = findUserById($peerId);
if (!$peer) {
  flash_set('error', 'User not found.');
  redirect('dashboard.php');
}
$peerName = (string)($peer['username'] ?? '');
$peerInitial = $peerName !== '' ? strtoupper($peerName[0]) : '?';

if (isPost()) {
  $message = getPost('message');
  if ($message !== '') {
    $sql = "INSERT INTO messages (from_user_id, to_user_id, message, created_at) VALUES (?, ?, ?, NOW())";
    $stmt = db()->prepare($sql);
    $stmt->execute([$userId, $peerId, $message]);
  }
  redirect('chat.php?user=' . $peerId);
}

ensureChatReadsTable();

$messages = [];
$sql = "
  SELECT id, from_user_id, to_user_id, message, created_at
  FROM messages
  WHERE (from_user_id = :uid AND to_user_id = :peer)
     OR (from_user_id = :peer AND to_user_id = :uid)
  ORDER BY created_at ASC, id ASC
  LIMIT 200
";
$stmt = db()->prepare($sql);
$stmt->execute(['uid' => $userId, 'peer' => $peerId]);
$messages = $stmt->fetchAll() ?: [];

$lastIncomingId = 0;
foreach ($messages as $msg) {
  if ((int)$msg['to_user_id'] === $userId && (int)$msg['from_user_id'] === $peerId) {
    $lastIncomingId = max($lastIncomingId, (int)$msg['id']);
  }
}

if ($lastIncomingId > 0) {
  $stmt = db()->prepare("
    INSERT INTO chat_reads (user_id, peer_id, last_read_message_id)
    VALUES (?, ?, ?)
    ON DUPLICATE KEY UPDATE last_read_message_id = GREATEST(last_read_message_id, VALUES(last_read_message_id))
  ");
  $stmt->execute([$userId, $peerId, $lastIncomingId]);
}

function formatMessageTime(string $timestamp): string {
  $time = strtotime($timestamp);
  if ($time === false) return '';
  return date('g:i A', $time);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>AfterSound - Chat</title>

  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Lilita+One&display=swap" rel="stylesheet" />

  <link rel="stylesheet" href="assets/css/style.css" />
</head>
<body>
  <div class="as-chat">
    <div class="asw-waves asw-waves-left" aria-hidden="true"></div>
    <div class="asw-waves asw-waves-right" aria-hidden="true"></div>

    <div class="chat-wrap">
      <div class="chat-card">
        <div class="chat-top">
          <div class="top-left">
            <button class="back-btn" type="button" aria-label="Back">
              <span>&#x2190;</span>
            </button>

            <div class="person">
              <div class="avatar" aria-label="User avatar">
                <?= e($peerInitial) ?>
                <span class="online-dot" aria-hidden="true"></span>
              </div>

              <div class="name-status">
                <div class="name"><?= e($peerName) ?></div>
                <div class="status">Online now</div>
              </div>
            </div>
          </div>

          <div class="top-actions">
            <button class="icon-btn" type="button" aria-label="Video call">
              <svg class="icon" viewBox="0 0 24 24" aria-hidden="true">
                <path d="M15 10.5V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2v-3.5l4 3v-9l-4 3z"/>
              </svg>
            </button>
            <button class="icon-btn" type="button" aria-label="Call">
              <svg class="icon" viewBox="0 0 24 24" aria-hidden="true">
                <path d="M6.6 10.8a15.2 15.2 0 0 0 6.6 6.6l2.2-2.2c.3-.3.7-.4 1.1-.3 1.2.4 2.5.6 3.8.6.6 0 1 .4 1 1V20c0 .6-.4 1-1 1C10.6 21 3 13.4 3 4c0-.6.4-1 1-1h3.9c.6 0 1 .4 1 1 0 1.3.2 2.6.6 3.8.1.4 0 .8-.3 1.1l-2.2 2.2z"/>
              </svg>
            </button>
          </div>
        </div>

        <div class="chat-body" id="chatBody">
          <?php if (!$messages): ?>
            <div class="day-pill">No messages yet</div>
          <?php endif; ?>

          <?php foreach ($messages as $msg): ?>
            <?php
              $isMine = (int)$msg['from_user_id'] === $userId;
              $rowClass = $isMine ? 'right' : 'left';
              $bubbleClass = $isMine ? 'right' : 'left';
              $timeText = formatMessageTime((string)$msg['created_at']);
            ?>
            <div class="msg-row <?= $rowClass ?>">
              <div class="bubble <?= $bubbleClass ?>">
                <p><?= nl2br(e((string)$msg['message'])) ?></p>
                <div class="meta-time">
                  <?= e($timeText) ?>
                  <?php if ($isMine): ?>
                    <span class="ticks" aria-hidden="true"><span class="tick"></span></span>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>

        <form class="composer" method="post" action="chat.php?user=<?= $peerId ?>">
          <button class="plus-btn" type="button" aria-label="Add">
            <span>&#xFF0B;</span>
          </button>

          <div class="input-shell">
            <input type="text" name="message" placeholder="Type a message..." autocomplete="off" required />
            <button class="emoji-btn" type="button" aria-label="Emoji">
              <span>&#x263A;</span>
            </button>
          </div>

          <button class="mic-btn" type="button" aria-label="Voice">
            <span>&#x1F3A4;</span>
          </button>
        </form>
      </div>
    </div>
  </div>

  <script>
    const chatBody = document.getElementById("chatBody");
    chatBody.scrollTop = chatBody.scrollHeight;

    const backBtn = document.querySelector(".back-btn");
    if (backBtn) {
      backBtn.addEventListener("click", () => {
        window.location.href = "dashboard.php";
      });
    }
  </script>
</body>
</html>
