<?php
$api_url = 'api.php';
?>
<!doctype html>
<html lang="en">

<head>
  <meta name="description"
    content="Gonubie Baptist Youth - Youth group for grades 7-12 in Gonubie, South Africa. Friday night Bible studies, games, events, and community.">
  <meta name="keywords"
    content="youth group, bible study, Gonubie Baptist, Christian youth, church youth, youth events, South Africa youth">
  <meta name="author" content="Gonubie Baptist Youth">
  <meta name="robots" content="index, follow">

  <meta property="og:title" content="Gonubie Baptist Youth">
  <meta property="og:description" content="Youth group events, Bible studies, and community in Gonubie">
  <meta property="og:type" content="website">
  <meta property="og:url" content="http://gonubieyouth.ddns.net/">

  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Gonubie Baptist Youth</title>
  <link rel="stylesheet" href="css/main.css" />
  <link rel="stylesheet" href="css/home.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
  <style>
    /* Loading state styles */
    .loading-placeholder {
      opacity: 0.6;
      display: inline-block;
    }

    .vote-count {
      font-size: 0.7rem;
      color: #f0b90b;
      margin-left: 0.5rem;
    }
  </style>
</head>

<body>
  <div class="header">
    <div class="left-container">
      <span>
        <i class="fa-solid fa-cross"></i>
        Gonubie Baptist Youth
      </span>
    </div>
    <div class="middle-container">
      <a href="index.php">home</a>
      <a href="past-lessons.php">past lessons</a>
      <a href="bible-verse.php">bible verse</a>
      <a href="voting.php">voting</a>
      <a href="gallery.php">gallery</a>
    </div>
    <div class="right-container">
      <i class="fa-solid fa-circle-user"></i>
    </div>
  </div>

  <!-- Hero Section -->
  <div class="landing-page">
    <div class="wrapper">
      <h1>Gonubie Baptist Youth</h1>
      <p>
        Two are better than one, because they have a good return for their
        labor: If either of them falls down, one can help the other up.
        -Ecclesiastes 4:9-10
      </p>
      <button class="cta-btn">
        Join Us This Friday <i class="fa-solid fa-arrow-right"></i>
      </button>
    </div>
  </div>

  <!-- Welcome / About Section -->
  <div class="welcome-section">
    <div class="container">
      <h2><i class="fa-solid fa-heart"></i> Welcome to GBY</h2>
      <p>
        A place where you belong, grow in faith, and build lasting
        friendships. Fridays at 7PM — come as you are!
      </p>
      <div class="features">
        <div class="feature">
          <i class="fa-solid fa-people"></i>
          <h3>Community</h3>
          <p>Real friendships & small groups</p>
        </div>
        <div class="feature">
          <i class="fa-solid fa-church"></i>
          <h3>Faith</h3>
          <p>Relevant talks & worship</p>
        </div>
        <div class="feature">
          <i class="fa-solid fa-gamepad"></i>
          <h3>Fun</h3>
          <p>Games, events & lock-ins</p>
        </div>
      </div>
    </div>
  </div>

  <!-- This Friday Section - Dynamically updated -->
  <div class="friday-section">
    <div class="container">
      <h2><i class="fa-solid fa-fire"></i> This Friday</h2>
      <div class="friday-grid">
        <div class="friday-card" id="topic-card">
          <i class="fa-solid fa-message"></i>
          <h3>Topic</h3>
          <p id="topic-text">Loading...</p>
          <div id="topic-description" class="topic-description"></div>
          <span id="topic-votes" class="vote-count"></span>
        </div>
        <div class="friday-card" id="game-card">
          <i class="fa-solid fa-dice-d6"></i>
          <h3>Game</h3>
          <p id="game-text">Loading...</p>
          <div id="game-rules" class="game-rules"></div>
          <span id="game-votes" class="vote-count"></span>
        </div>
        <div class="friday-card">
          <i class="fa-solid fa-clock"></i>
          <h3>Time & Place</h3>
          <p>Friday 7PM @ Gonubie Baptist Church Hall</p>
        </div>
      </div>
    </div>
  </div>

  <!-- Upcoming Events -->
  <div class="events-section">
    <div class="container">
      <h2><i class="fa-solid fa-calendar"></i> Upcoming Events</h2>
      <div class="events-list">
        <div class="event-item-hidden">
          <div class="event-date">May 9</div>
          <div class="event-info">
            <h3>Campout Under the Stars</h3>
            <p>Bring your tent and sleeping bag!</p>
          </div>
        </div>
        <div class="event-item-hidden">
          <div class="event-date">May 16</div>
          <div class="event-info">
            <h3>Coffeehouse & Worship Night</h3>
            <p>Acoustic worship + hot chocolate</p>
          </div>
        </div>
        <div class="event-item-hidden">
          <div class="event-date">May 23</div>
          <div class="event-info">
            <h3>Sports Day + Braai</h3>
            <p>Soccer, volleyball and a good old braai</p>
          </div>
        </div>
        <div class="event-item-hidden">
          <div class="event-date">May 30</div>
          <div class="event-info">
            <h3>Glow Party Lock-in</h3>
            <p>7PM – 7AM. Don't miss it!</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Bible Verse of the Day -->
  <div class="verse-section">
    <div class="container">
      <i class="fa-solid fa-bible"></i>
      <h3>Verse of the Day</h3>
      <p class="verse-text">
        "I can do all things through Christ who strengthens me." — Philippians
        4:13
      </p>
    </div>
  </div>

  <!-- New Members Info -->
  <div class="join-section">
    <div class="container">
      <h2><i class="fa-regular fa-heart"></i> New Here?</h2>
      <p><strong>Grades 7–12</strong> | Fridays 7PM – 9PM</p>
      <p><strong>Where:</strong> Gonubie Baptist Church, Main Rd, Gonubie</p>
      <button class="contact-btn">
        <i class="fa-brands fa-whatsapp"></i> Contact Us
      </button>
    </div>
  </div>

  <!-- Footer -->
  <div class="footer">
    <div class="container">
      <div class="footer-content">
        <p>&copy; 2026 Gonubie Baptist Youth</p>
        <div class="social-icons">
          <i class="fa-brands fa-instagram"></i>
          <i class="fa-brands fa-whatsapp"></i>
          <i class="fa-solid fa-envelope"></i>
        </div>
      </div>
    </div>
  </div>

  <script>
    // Fetch and display the current winning topic and game
    async function loadWinners() {
      try {
        const response = await fetch('<?php echo $api_url; ?>?action=get_winners');
        const result = await response.json();

        if (result.success) {
          // Update topic section
          document.getElementById('topic-text').innerHTML = `"${result.data.topic.name}"`;
          document.getElementById('topic-votes').innerHTML = `${result.data.topic.votes} vote${result.data.topic.votes !== 1 ? 's' : ''}`;

          // Add topic description
          if (result.data.topic.description) {
            document.getElementById('topic-description').innerHTML = `<i class="fa-regular fa-lightbulb"></i> ${result.data.topic.description}`;
          }

          // Update game section
          document.getElementById('game-text').innerHTML = result.data.game.name;
          document.getElementById('game-votes').innerHTML = `${result.data.game.votes} vote${result.data.game.votes !== 1 ? 's' : ''}`;

          // Add game rules
          if (result.data.game.game_rules) {
            document.getElementById('game-rules').innerHTML = `<i class="fa-solid fa-dice"></i> ${result.data.game.game_rules.replace(/\n/g, '<br>')}`;
          }
        }
      } catch (error) {
        console.error('Error loading winners:', error);
      }
    }

    // Load winners when page loads
    document.addEventListener('DOMContentLoaded', loadWinners);

    // Optional: Auto-refresh every 30 seconds to show live vote updates
    setInterval(loadWinners, 30000);
  </script>
</body>

</html>