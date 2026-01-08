<?php
// speak-chat/profile.php
require_once __DIR__ . '/config/db.php';

// Protect page
requireLogin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>AfterSound - My Profile</title>

  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Lilita+One&display=swap" rel="stylesheet" />

  <link rel="stylesheet" href="assets/css/style.css" />
</head>

<body>
  <div class="aftersound-profile2">
    <div class="asw-waves asw-waves-left" aria-hidden="true"></div>
    <div class="asw-waves asw-waves-right" aria-hidden="true"></div>

    <header class="p2-top as-pop-1">
      <button class="p2-iconbtn" id="goBack" aria-label="Back">
        <span class="p2-ico">&#x2190;</span>
      </button>

      <div class="p2-title">MY PROFILE</div>

      <button class="p2-iconbtn" id="editBtn" aria-label="Edit profile">
        <span class="p2-ico">&#x270E;</span>
      </button>
    </header>

    <main class="p2-main">
      <section class="p2-hero as-pop-2">
        <div class="p2-avatar-wrap">
          <div class="p2-ring">
            <div class="p2-avatar" id="avatarCircle">A</div>
          </div>

          <button class="p2-camera" aria-label="Change photo" title="Change photo">
            &#x1F4F7;
          </button>
        </div>

        <h1 class="p2-name" id="profileName">Sarah Sound</h1>
        <div class="p2-handle" id="profileHandle">@sarah_vibes</div>

        <div class="p2-chips">
          <span class="p2-chip p2-chip-online">Online</span>
          <span class="p2-chip p2-chip-level">Level 5</span>
        </div>
      </section>

      <section class="p2-card p2-card-about as-pop-3">
        <div class="p2-card-title">About Me</div>
        <p class="p2-card-text" id="aboutText">
          Music lover, vinyl collector, and conversation enthusiast. Here for the deep talks and good vibes only. &#x1F3A7; &#x2728;
        </p>

        <div class="p2-tags">
          <span class="p2-tag">#Music</span>
          <span class="p2-tag">#Podcasts</span>
          <span class="p2-tag">#LateNightChats</span>
        </div>
      </section>

      <section class="p2-section as-pop-3">
        <div class="p2-section-head">Current Vibe</div>

        <div class="p2-rowcard">
          <div class="p2-rowicon">&#x1F3B5;</div>
          <div class="p2-rowtext">
            <div class="p2-rowtitle">Listening to</div>
            <div class="p2-rowsub" id="vibeText">Glass Animals - Heat Waves</div>
          </div>
          <div class="p2-chevron">&#x203A;</div>
        </div>
      </section>

      <section class="p2-section as-pop-4">
        <div class="p2-section-head">Settings</div>

        <div class="p2-settings">
          <a class="p2-rowcard p2-rowlink" href="#">
            <div class="p2-rowicon soft-blue">&#x1F464;</div>
            <div class="p2-rowtext">
              <div class="p2-rowtitle">Account</div>
            </div>
            <div class="p2-chevron">&#x203A;</div>
          </a>

          <a class="p2-rowcard p2-rowlink" href="#">
            <div class="p2-rowicon soft-purple">&#x1F514;</div>
            <div class="p2-rowtext">
              <div class="p2-rowtitle">Notifications</div>
            </div>
            <div class="p2-chevron">&#x203A;</div>
          </a>

          <a class="p2-rowcard p2-rowlink" href="#">
            <div class="p2-rowicon soft-green">&#x1F512;</div>
            <div class="p2-rowtext">
              <div class="p2-rowtitle">Privacy</div>
            </div>
            <div class="p2-chevron">&#x203A;</div>
          </a>
        </div>
      </section>

      <section class="p2-logout as-pop-4">
        <button class="p2-logout-btn" id="logoutBtn">
          <span class="p2-logout-ico">&#x21E6;</span>
          LOG OUT
        </button>

        <div class="p2-version">Version 2.4.0</div>
      </section>
    </main>

    <div class="p2-modal" id="modal" aria-hidden="true">
      <div class="p2-modal-card">
        <div class="p2-modal-head">
          <div class="p2-modal-title">Edit Profile</div>
          <button class="p2-modal-close" id="closeModal" aria-label="Close">&#x2715;</button>
        </div>

        <form class="p2-form" id="profileForm">
          <label class="p2-label">Name</label>
          <input class="p2-input" id="nameInput" type="text" placeholder="Your name" />

          <label class="p2-label">Username</label>
          <input class="p2-input" id="userInput" type="text" placeholder="@your_handle" />

          <label class="p2-label">About</label>
          <textarea class="p2-textarea" id="aboutInput" placeholder="Tell your vibe..."></textarea>

          <label class="p2-label">Current Vibe</label>
          <input class="p2-input" id="vibeInput" type="text" placeholder="Song or mood..." />

          <button class="p2-save" type="submit">
            <span class="p2-save-inner">save</span>
          </button>
        </form>
      </div>
    </div>
  </div>

  <script>
    document.getElementById("goBack").addEventListener("click", () => {
      window.location.href = "dashboard.php";
    });

    const modal = document.getElementById("modal");
    const openBtn = document.getElementById("editBtn");
    const closeBtn = document.getElementById("closeModal");

    function openModal(){
      modal.classList.add("open");
      modal.setAttribute("aria-hidden", "false");
    }
    function closeModal(){
      modal.classList.remove("open");
      modal.setAttribute("aria-hidden", "true");
    }

    openBtn.addEventListener("click", openModal);
    closeBtn.addEventListener("click", closeModal);
    modal.addEventListener("click", (e) => {
      if (e.target === modal) closeModal();
    });

    const defaultAbout = "Music lover, vinyl collector, and conversation enthusiast. Here for the deep talks and good vibes only. \u{1F3A7} \u{2728}";
    const savedName  = localStorage.getItem("aftersoundName")  || "Sarah Sound";
    const savedUser  = localStorage.getItem("aftersoundUser")  || "@sarah_vibes";
    const savedAbout = localStorage.getItem("aftersoundAbout") || defaultAbout;
    const savedVibe  = localStorage.getItem("aftersoundVibe")  || "Glass Animals - Heat Waves";

    document.getElementById("profileName").textContent = savedName;
    document.getElementById("profileHandle").textContent = savedUser;
    document.getElementById("aboutText").textContent = savedAbout;
    document.getElementById("vibeText").textContent = savedVibe;

    const avatarCircle = document.getElementById("avatarCircle");
    avatarCircle.textContent = (savedName.trim()[0] || "A").toUpperCase();

    document.getElementById("nameInput").value = savedName;
    document.getElementById("userInput").value = savedUser;
    document.getElementById("aboutInput").value = savedAbout;
    document.getElementById("vibeInput").value = savedVibe;

    document.getElementById("profileForm").addEventListener("submit", (e) => {
      e.preventDefault();

      const name = document.getElementById("nameInput").value.trim() || "AfterSound";
      const user = document.getElementById("userInput").value.trim() || "@aftersound";
      const about = document.getElementById("aboutInput").value.trim();
      const vibe = document.getElementById("vibeInput").value.trim();

      localStorage.setItem("aftersoundName", name);
      localStorage.setItem("aftersoundUser", user);
      localStorage.setItem("aftersoundAbout", about);
      localStorage.setItem("aftersoundVibe", vibe);

      document.getElementById("profileName").textContent = name;
      document.getElementById("profileHandle").textContent = user;
      document.getElementById("aboutText").textContent = about;
      document.getElementById("vibeText").textContent = vibe;
      avatarCircle.textContent = (name.trim()[0] || "A").toUpperCase();

      closeModal();
    });

    document.getElementById("logoutBtn").addEventListener("click", () => {
      window.location.href = "logout.php";
    });
  </script>
</body>
</html>