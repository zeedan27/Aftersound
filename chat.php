<?php
// speak-chat/chat.php
require_once __DIR__ . '/config/db.php';

// Protect page
requireLogin();
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
                M
                <span class="online-dot" aria-hidden="true"></span>
              </div>

              <div class="name-status">
                <div class="name">Maya Z.</div>
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
          <div class="day-pill">Today, 10:23 AM</div>

          <div class="msg-row left">
            <div class="bubble left">
              <p>Hey! Did you hear that new remix I sent over? &#x1F3B5;</p>
              <div class="meta-time">10:23 AM</div>
            </div>
          </div>

          <div class="msg-row right">
            <div class="bubble right">
              <p>Yesss! It&#x27;s absolutely fire &#x1F525; The bass drop at 2:30 is insane.</p>
              <div class="meta-time">10:24 AM <span class="ticks" aria-hidden="true"><span class="tick"></span></span></div>
            </div>
          </div>

          <div class="msg-row left">
            <div class="bubble voice">
              <div class="play" aria-hidden="true"></div>
              <div class="wavebar" aria-hidden="true">
                <span class="bar"></span><span class="bar"></span><span class="bar"></span>
                <span class="bar"></span><span class="bar"></span><span class="bar"></span>
              </div>
              <div class="dur">0:14</div>
              <div class="meta-time" style="margin-top:0;color:rgba(0,0,0,.35);">10:25 AM</div>
            </div>
          </div>

          <div class="msg-row right">
            <div class="bubble img-card right">
              <img class="img" src="https://images.unsplash.com/photo-1455885666463-4d0b9e36a7f8?auto=format&amp;fit=crop&amp;w=1200&amp;q=70" alt="Shared memory" />
              <div class="cap">Reminds me of this night!</div>
              <div class="meta-time" style="padding:0 18px 16px;">10:26 AM <span class="ticks" aria-hidden="true"><span class="tick"></span></span></div>
            </div>
          </div>
        </div>

        <div class="composer">
          <button class="plus-btn" type="button" aria-label="Add">
            <span>&#xFF0B;</span>
          </button>

          <div class="input-shell">
            <input type="text" placeholder="Type a message..." />
            <button class="emoji-btn" type="button" aria-label="Emoji">
              <span>&#x263A;</span>
            </button>
          </div>

          <button class="mic-btn" type="button" aria-label="Voice">
            <span>&#x1F3A4;</span>
          </button>
        </div>
      </div>
    </div>
  </div>

  <script>
    const chatBody = document.getElementById("chatBody");
    chatBody.scrollTop = chatBody.scrollHeight;
  </script>
</body>
</html>