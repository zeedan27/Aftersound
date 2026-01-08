<?php
// speak-chat/dashboard.php
require_once __DIR__ . '/config/db.php';

// Protect page
requireLogin();

$username = currentUsername();
$success = flash_get('success');
$error   = flash_get('error');
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
        <div class="search-bar">
          <span class="search-icon" aria-hidden="true">??</span>
          <input
            id="searchInput"
            type="text"
            placeholder="Search conversations..."
            aria-label="Search conversations"
          />
        </div>
      </section>

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

      <section class="chats-card as-pop-3">
        <div class="chats-head">
          <div class="chats-title">
            <span class="chat-icon" aria-hidden="true">??</span>
            <span>Recent Chats</span>
          </div>
        </div>

        <div class="chat-list" id="chatList">
          <a class="chat-item" href="#">
            <div class="avatar">
              <div class="status online" aria-hidden="true"></div>
              <div class="avatar-circle">D</div>
            </div>
            <div class="chat-info">
              <div class="chat-toprow">
                <div class="chat-name">Dj Kaleb</div>
                <div class="chat-time hot">10:42 AM</div>
              </div>
              <div class="chat-preview">Yo! Did you see that so...</div>
            </div>
            <div class="chat-meta">
              <div class="badge red">2</div>
            </div>
          </a>

          <a class="chat-item" href="#">
            <div class="avatar">
              <div class="avatar-circle dark">?</div>
            </div>
            <div class="chat-info">
              <div class="chat-toprow">
                <div class="chat-name">Music Group ??</div>
                <div class="chat-time">9:15 AM</div>
              </div>
              <div class="chat-preview">Sarah: Let''s meet at the...</div>
            </div>
            <div class="chat-meta"></div>
          </a>

          <a class="chat-item" href="#">
            <div class="avatar">
              <div class="status online" aria-hidden="true"></div>
              <div class="avatar-circle">A</div>
            </div>
            <div class="chat-info">
              <div class="chat-toprow">
                <div class="chat-name">Alex Chen</div>
                <div class="chat-time hot">Yesterday</div>
              </div>
              <div class="chat-preview">Voice message (0:42)</div>
            </div>
            <div class="chat-meta">
              <div class="badge red">1</div>
            </div>
          </a>

          <a class="chat-item" href="#">
            <div class="avatar">
              <div class="avatar-circle muted">S</div>
            </div>
            <div class="chat-info">
              <div class="chat-toprow">
                <div class="chat-name">Soundcloud Support</div>
                <div class="chat-time">Mon</div>
              </div>
              <div class="chat-preview">Your ticket has been up...</div>
            </div>
            <div class="chat-meta"></div>
          </a>

          <a class="chat-item" href="#">
            <div class="avatar">
              <div class="avatar-circle muted">B</div>
            </div>
            <div class="chat-info">
              <div class="chat-toprow">
                <div class="chat-name">Beats & Bytes</div>
                <div class="chat-time">Sun</div>
              </div>
              <div class="chat-preview">Check out the new sam...</div>
            </div>
            <div class="chat-meta"></div>
          </a>
        </div>
      </section>

      <button class="fab as-pop-4" aria-label="New chat">
        ??
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