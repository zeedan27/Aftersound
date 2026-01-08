<?php
// speak-chat/dashboard.php
require_once __DIR__ . '/config/db.php';

// Protect page
requireLogin();

$username = currentUsername();
$success = flash_get('success');
$error   = flash_get('error');
$userId  = currentUserId();
$searchQuery = trim((string)($_GET['q'] ?? ''));
$searchResults = [];

function formatChatTime(string $timestamp): string {
  $time = strtotime($timestamp);
  if ($time === false) return '';
  $today = date('Y-m-d');
  $day = date('Y-m-d', $time);
  if ($day === $today) return date('g:i A', $time);
  if ($day === date('Y-m-d', strtotime('-1 day'))) return 'Yesterday';
  if (date('W', $time) === date('W')) return date('D', $time);
  return date('M j', $time);
}

$threads = [];
if ($userId !== null) {
  ensureChatReadsTable();
  $sql = "
    SELECT m.id, m.from_user_id, m.to_user_id, m.message, m.created_at,
           u.id AS peer_id, u.username AS peer_name,
           COALESCE((
             SELECT COUNT(*)
             FROM messages mu
             WHERE mu.from_user_id = u.id
               AND mu.to_user_id = :uid4
               AND mu.id > COALESCE(cr.last_read_message_id, 0)
           ), 0) AS unread_count
    FROM messages m
    JOIN (
      SELECT
        CASE WHEN from_user_id = :uid1 THEN to_user_id ELSE from_user_id END AS peer_id,
        MAX(id) AS max_id
      FROM messages
      WHERE from_user_id = :uid2 OR to_user_id = :uid3
      GROUP BY peer_id
    ) lm ON lm.max_id = m.id
    JOIN users u ON u.id = lm.peer_id
    LEFT JOIN chat_reads cr ON cr.user_id = :uid5 AND cr.peer_id = u.id
    ORDER BY m.created_at DESC
  ";
  $stmt = db()->prepare($sql);
  $stmt->execute([
    'uid1' => $userId,
    'uid2' => $userId,
    'uid3' => $userId,
    'uid4' => $userId,
    'uid5' => $userId,
  ]);
  $threads = $stmt->fetchAll() ?: [];
}

if ($userId !== null && $searchQuery !== '') {
  $sql = "
    SELECT
      u.id,
      u.username,
      u.email,
      (
        SELECT fr.id
        FROM friend_requests fr
        WHERE fr.from_user_id = u.id
          AND fr.to_user_id = :uid1
          AND fr.status = 'pending'
        LIMIT 1
      ) AS pending_in_id,
      (
        SELECT fr.id
        FROM friend_requests fr
        WHERE fr.from_user_id = :uid2
          AND fr.to_user_id = u.id
          AND fr.status = 'pending'
        LIMIT 1
      ) AS pending_out_id,
      (
        SELECT 1
        FROM friend_requests fr
        WHERE fr.status = 'accepted'
          AND (
            (fr.from_user_id = :uid3 AND fr.to_user_id = u.id)
            OR
            (fr.from_user_id = u.id AND fr.to_user_id = :uid4)
          )
        LIMIT 1
      ) AS is_friend
    FROM users
    WHERE u.id <> :uid5
      AND (u.username LIKE :q1 OR u.email LIKE :q2)
    ORDER BY username
    LIMIT 20
  ";
  $stmt = db()->prepare($sql);
  $stmt->execute([
    'uid1' => $userId,
    'uid2' => $userId,
    'uid3' => $userId,
    'uid4' => $userId,
    'uid5' => $userId,
    'q1' => '%' . $searchQuery . '%',
    'q2' => '%' . $searchQuery . '%',
  ]);
  $searchResults = $stmt->fetchAll() ?: [];
}

$incomingRequests = [];
if ($userId !== null) {
  $sql = "
    SELECT fr.id, fr.created_at, u.id AS from_id, u.username, u.email
    FROM friend_requests fr
    JOIN users u ON u.id = fr.from_user_id
    WHERE fr.to_user_id = :uid AND fr.status = 'pending'
    ORDER BY fr.created_at DESC
  ";
  $stmt = db()->prepare($sql);
  $stmt->execute(['uid' => $userId]);
  $incomingRequests = $stmt->fetchAll() ?: [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>AfterSound - Dashboard</title>

  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Lilita+One&display=swap" rel="stylesheet" />

  <link rel="stylesheet" href="assets/css/style.css" />
</head>

<body>
  <div class="aftersound-dash">
    <div class="asw-waves asw-waves-left" aria-hidden="true"></div>
    <div class="asw-waves asw-waves-right" aria-hidden="true"></div>

    <header class="dash-top as-pop-1">
      <div class="dash-brand">
        <span class="brand-pill brand-pill-yellow">AFTER</span>
        <span class="brand-pill brand-pill-red">SOUND</span>
      </div>

      <button class="profile-badge" id="profileBadge" aria-label="Profile">
        <span id="profileLetter">A</span>
      </button>
    </header>

    <main class="dash-main">
      <section class="search-wrap as-pop-2">
        <form class="search-bar" method="get" action="dashboard.php">
          <span class="search-icon" aria-hidden="true">&#x1F50D;</span>
          <input
            id="searchInput"
            name="q"
            type="text"
            value="<?= e($searchQuery) ?>"
            placeholder="Search conversations or users..."
            aria-label="Search conversations or users"
          />
          <button class="search-btn" type="submit">Search</button>
        </form>
      </section>

      <?php if ($searchQuery !== ''): ?>
        <section class="search-results as-pop-3" aria-label="Search results">
          <div class="search-results-head">
            Search results for "<?= e($searchQuery) ?>"
          </div>
          <div class="search-results-list">
            <?php if (!$searchResults): ?>
              <div class="search-result">
                <div class="result-info">
                  <div class="result-name">No users found</div>
                  <div class="result-meta">Try a different name or email.</div>
                </div>
              </div>
            <?php else: ?>
              <?php foreach ($searchResults as $result): ?>
                <?php
                  $peerId = (int)$result['id'];
                  $peerName = (string)$result['username'];
                  $peerEmail = (string)$result['email'];
                  $pendingInId = (int)($result['pending_in_id'] ?? 0);
                  $pendingOutId = (int)($result['pending_out_id'] ?? 0);
                  $isFriend = (bool)($result['is_friend'] ?? false);
                  $initial = $peerName !== '' ? strtoupper($peerName[0]) : '?';
                ?>
                <div class="search-result">
                  <div class="result-avatar"><?= e($initial) ?></div>
                  <div class="result-info">
                    <div class="result-name"><?= e($peerName) ?></div>
                    <div class="result-meta"><?= e($peerEmail) ?></div>
                  </div>
                  <div class="result-actions">
                    <?php if ($isFriend): ?>
                      <span class="result-badge">Friend</span>
                    <?php elseif ($pendingOutId): ?>
                      <span class="result-badge">Request sent</span>
                    <?php elseif ($pendingInId): ?>
                      <form method="post" action="friend-request.php">
                        <input type="hidden" name="request_id" value="<?= $pendingInId ?>" />
                        <input type="hidden" name="action" value="accept" />
                        <input type="hidden" name="q" value="<?= e($searchQuery) ?>" />
                        <button class="result-btn" type="submit">Accept</button>
                      </form>
                      <form method="post" action="friend-request.php">
                        <input type="hidden" name="request_id" value="<?= $pendingInId ?>" />
                        <input type="hidden" name="action" value="deny" />
                        <input type="hidden" name="q" value="<?= e($searchQuery) ?>" />
                        <button class="result-btn result-btn-secondary" type="submit">Decline</button>
                      </form>
                    <?php else: ?>
                      <form method="post" action="add-friend.php">
                        <input type="hidden" name="friend_id" value="<?= $peerId ?>" />
                        <input type="hidden" name="q" value="<?= e($searchQuery) ?>" />
                        <button class="result-btn" type="submit">Add</button>
                      </form>
                    <?php endif; ?>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>
        </section>
      <?php endif; ?>

      <?php if ($incomingRequests): ?>
        <section class="requests as-pop-3" aria-label="Friend requests">
          <div class="requests-head">Friend Requests</div>
          <div class="requests-list">
            <?php foreach ($incomingRequests as $req): ?>
              <?php
                $fromName = (string)$req['username'];
                $fromEmail = (string)$req['email'];
                $initial = $fromName !== '' ? strtoupper($fromName[0]) : '?';
              ?>
              <div class="request-item">
                <div class="result-avatar"><?= e($initial) ?></div>
                <div class="result-info">
                  <div class="result-name"><?= e($fromName) ?></div>
                  <div class="result-meta"><?= e($fromEmail) ?></div>
                </div>
                <div class="result-actions">
                  <form method="post" action="friend-request.php">
                    <input type="hidden" name="request_id" value="<?= (int)$req['id'] ?>" />
                    <input type="hidden" name="action" value="accept" />
                    <button class="result-btn" type="submit">Accept</button>
                  </form>
                  <form method="post" action="friend-request.php">
                    <input type="hidden" name="request_id" value="<?= (int)$req['id'] ?>" />
                    <input type="hidden" name="action" value="deny" />
                    <button class="result-btn result-btn-secondary" type="submit">Decline</button>
                  </form>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </section>
      <?php endif; ?>

      <section class="stories as-pop-2" aria-label="Stories">
        <div class="story story-add">
          <div class="story-circle dashed">
            <span class="plus">+</span>
          </div>
          <div class="story-name">My Story</div>
        </div>

        <div class="story">
          <div class="story-circle ring"><img alt="" src="" /></div>
          <div class="story-name">Sarah</div>
        </div>

        <div class="story">
          <div class="story-circle ring"><img alt="" src="" /></div>
          <div class="story-name">Mike</div>
        </div>

        <div class="story">
          <div class="story-circle ring"><img alt="" src="" /></div>
          <div class="story-name">Eliza</div>
        </div>
      </section>

      <?php if ($success): ?>
        <div class="dash-alert dash-alert-success as-pop-3"><?= e($success) ?></div>
      <?php endif; ?>

      <?php if ($error): ?>
        <div class="dash-alert dash-alert-error as-pop-3"><?= e($error) ?></div>
      <?php endif; ?>

      <section class="chats-card as-pop-3">
        <div class="chats-head">
          <div class="chats-title">
            <span class="chat-icon" aria-hidden="true">&#x1F4AC;</span>
            <span>Recent Chats</span>
          </div>
        </div>

        <div class="chat-list" id="chatList">
          <?php if (!$threads): ?>
            <div class="chat-item" style="pointer-events:none;">
              <div class="avatar">
                <div class="avatar-circle muted">?</div>
              </div>
              <div class="chat-info">
                <div class="chat-toprow">
                  <div class="chat-name">No chats yet</div>
                  <div class="chat-time">--</div>
                </div>
                <div class="chat-preview">Start a conversation to see it here.</div>
              </div>
              <div class="chat-meta"></div>
            </div>
          <?php else: ?>
            <?php foreach ($threads as $thread): ?>
              <?php
                $peerName = (string)$thread['peer_name'];
                $preview = preg_replace('/\s+/', ' ', trim((string)$thread['message']));
                $maxLen = 36;
                if (function_exists('mb_strlen') && function_exists('mb_substr')) {
                  if (mb_strlen($preview, 'UTF-8') > $maxLen) {
                    $preview = mb_substr($preview, 0, $maxLen, 'UTF-8') . '...';
                  }
                } elseif (strlen($preview) > $maxLen) {
                  $preview = substr($preview, 0, $maxLen) . '...';
                }
                $initial = $peerName !== '' ? strtoupper($peerName[0]) : '?';
              ?>
              <a class="chat-item" href="chat.php?user=<?= (int)$thread['peer_id'] ?>">
                <div class="avatar">
                  <div class="avatar-circle"><?= e($initial) ?></div>
                </div>
                <div class="chat-info">
                  <div class="chat-toprow">
                    <div class="chat-name"><?= e($peerName) ?></div>
                    <div class="chat-time"><?= e(formatChatTime((string)$thread['created_at'])) ?></div>
                  </div>
                  <div class="chat-preview"><?= e($preview) ?></div>
                </div>
                <div class="chat-meta">
                  <?php if (!empty($thread['unread_count'])): ?>
                    <div class="badge red"><?= (int)$thread['unread_count'] ?></div>
                  <?php endif; ?>
                </div>
              </a>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </section>

      <button class="fab as-pop-4" aria-label="New chat">
        &#x270F;
      </button>
    </main>
  </div>

  <script>
    // --- Profile letter: uses saved name if available ---
    // Set this from your signup/login later: localStorage.setItem("aftersoundName", "Sarah");
    const name = (localStorage.getItem("aftersoundName") || "AfterSound").trim();
    const firstLetter = name ? name[0].toUpperCase() : "A";
    document.getElementById("profileLetter").textContent = firstLetter;

    // Navigate to profile edit page
    document.getElementById("profileBadge").addEventListener("click", () => {
      window.location.href = "profile.php";
    });

    // Search filter
    const searchInput = document.getElementById("searchInput");
    const chatList = document.getElementById("chatList");
    const chatItems = Array.from(chatList.querySelectorAll(".chat-item"));

    searchInput.addEventListener("input", (e) => {
      const q = e.target.value.toLowerCase().trim();
      chatItems.forEach((item) => {
        const name = item.querySelector(".chat-name")?.textContent.toLowerCase() || "";
        const prev = item.querySelector(".chat-preview")?.textContent.toLowerCase() || "";
        item.style.display = (name.includes(q) || prev.includes(q)) ? "flex" : "none";
      });
    });
  </script>
</body>
</html>
