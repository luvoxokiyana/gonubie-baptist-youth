<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gonubie Baptist Youth - Voting</title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/voting.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="header">
        <div class="left-container">
            <span><i class="fa-solid fa-cross"></i> Gonubie Baptist Youth</span>
        </div>
        <div class="middle-container">
            <a href="home.html">home</a>
            <a href="past-lessons.php">past lessons</a>
            <a href="bible-verse.php">bible verse</a>
            <a href="voting.php">voting</a>
            <a href="gallery.php">gallery</a>
        </div>
        <div class="right-container">
            <i class="fa-solid fa-circle-user"></i>
        </div>
    </div>

    <div class="voting-page">
        <div class="container">
            <div class="voting-header">
                <h1><i class="fa-solid fa-square-poll-vertical"></i> Youth Votes</h1>
                <p>Your voice matters! Vote for what you want to see at youth.</p>
            </div>

            <div class="poll-grid">
                <div class="poll-card" id="poll-bible">
                    <h2><i class="fa-solid fa-bible"></i> Next Bible Study</h2>
                    <div class="poll-description">What topic should we study next Friday?</div>
                    <div class="poll-options" id="bible-options"></div>
                    <button class="vote-btn" id="vote-bible-btn">🗳️ Cast Vote</button>
                    <div class="results" id="bible-results"></div>
                    <div id="bible-voted-message"></div>
                </div>

                <div class="poll-card" id="poll-game">
                    <h2><i class="fa-solid fa-gamepad"></i> Next Game</h2>
                    <div class="poll-description">Which game do you want to play?</div>
                    <div class="poll-options" id="game-options"></div>
                    <button class="vote-btn" id="vote-game-btn">🎮 Cast Vote</button>
                    <div class="results" id="game-results"></div>
                    <div id="game-voted-message"></div>
                </div>

                <div class="poll-card" id="poll-event">
                    <h2><i class="fa-regular fa-calendar"></i> Future Event</h2>
                    <div class="poll-description">What event should we plan next month?</div>
                    <div class="poll-options" id="event-options"></div>
                    <button class="vote-btn" id="vote-event-btn">📅 Cast Vote</button>
                    <div class="results" id="event-results"></div>
                    <div id="event-voted-message"></div>
                </div>
            </div>

            <div class="suggestion-box">
                <h3><i class="fa-regular fa-lightbulb"></i> Have an idea?</h3>
                <p style="color: #8b949e; font-size: 0.85rem;">Suggest a topic, game, or event for future polls!</p>
                <div class="suggestion-input">
                    <input type="text" id="suggestion-input" placeholder="e.g., 'Bowling night' or 'Friendship Bible study'">
                    <button id="submit-suggestion"><i class="fa-regular fa-paper-plane"></i> Suggest</button>
                </div>
                <div class="suggestions-list" id="suggestions-list"></div>
            </div>
        </div>
    </div>

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

    <script src="js/voting.js"></script>
</body>
</html>