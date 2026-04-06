<?php
session_start();
$is_logged_in = isset($_SESSION['member_id']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gonubie Baptist Youth - Voting</title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/voting.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Login wall styles */
        .login-wall {
            max-width: 500px;
            margin: 60px auto;
            padding: 2rem;
            background: #161b22;
            border-radius: 16px;
            text-align: center;
            border: 1px solid #30363d;
        }

        .login-wall i {
            font-size: 3rem;
            color: #f0b90b;
            margin-bottom: 1rem;
        }

        .login-wall h2 {
            color: #fff;
            margin-bottom: 0.5rem;
        }

        .login-wall p {
            color: #8b949e;
            margin-bottom: 1.5rem;
        }

        .login-wall button {
            background: #f0b90b;
            color: #0d1117;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            font-size: 1rem;
        }

        .login-wall button:hover {
            background: #d4a00a;
        }

        .user-status {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.3rem 0.8rem;
            border-radius: 50px;
        }

        .user-status span {
            color: #e6edf3;
            font-size: 0.85rem;
        }

        .user-status a {
            color: #f0b90b;
            text-decoration: none;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="left-container">
            <span><i class="fa-solid fa-cross"></i> Gonubie Baptist Youth</span>
        </div>
        <div class="middle-container">
            <a href="index.php">home</a>
            <a href="past-lessons.php">past lessons</a>
            <a href="bible-verse.php">bible verse</a>
            <a href="voting.php">voting</a>
            <a href="gallery.php">gallery</a>
        </div>
        <div class="right-container">
            <?php if ($is_logged_in): ?>
                <div class="user-status">
                    <i class="fa-solid fa-circle-user"></i>
                </div>
            <?php else: ?>
                <a href="login.php?redirect=voting.php" style="color: #f0b90b;">
                    <i class="fa-solid fa-circle-user"></i>
                </a>
            <?php endif; ?>
        </div>
    </div>

    <?php if (!$is_logged_in): ?>
        <div class="login-wall">
            <i class="fa-solid fa-lock"></i>
            <h2>Voting Restricted</h2>
            <p>Only youth members can vote on topics and games.<br>Please login to participate.</p>
            <a href="login.php?redirect=voting.php"><button>🔑 Login to Vote</button></a>
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
    <?php else: ?>
        <div class="voting-page">
            <div class="container">
                <div class="voting-header">
                    <h1><i class="fa-solid fa-square-poll-vertical"></i> Youth Votes</h1>
                    <p>Your voice matters! Vote for what you want to see at youth.</p>
                </div>

                <div class="poll-grid">
                    <!-- Bible Study Poll -->
                    <div class="poll-card" id="poll-bible">
                        <h2><i class="fa-solid fa-bible"></i> Next Bible Study</h2>
                        <div class="poll-description">What topic should we study next Friday?</div>
                        <div class="poll-options" id="bible-options"></div>
                        <button class="vote-btn" id="vote-bible-btn">🗳️ Cast Vote</button>
                        <div class="results" id="bible-results"></div>
                        <div id="bible-voted-message"></div>
                    </div>

                    <!-- Game Poll -->
                    <div class="poll-card" id="poll-game">
                        <h2><i class="fa-solid fa-gamepad"></i> Next Game</h2>
                        <div class="poll-description">Which game do you want to play?</div>
                        <div class="poll-options" id="game-options"></div>
                        <button class="vote-btn" id="vote-game-btn">🎮 Cast Vote</button>
                        <div class="results" id="game-results"></div>
                        <div id="game-voted-message"></div>
                    </div>

                    <!-- Event Poll -->
                    <div class="poll-card" id="poll-event">
                        <h2><i class="fa-regular fa-calendar"></i> Future Event</h2>
                        <div class="poll-description">What event should we plan next month?</div>
                        <div class="poll-options" id="event-options"></div>
                        <button class="vote-btn" id="vote-event-btn">📅 Cast Vote</button>
                        <div class="results" id="event-results"></div>
                        <div id="event-voted-message"></div>
                    </div>
                </div>

                <!-- Description Panel - Shows details of selected option -->
                <div class="description-panel" id="description-panel">
                    <div class="description-header">
                        <i class="fa-regular fa-lightbulb"></i>
                        <h3>About this option</h3>
                    </div>
                    <div class="description-content" id="description-content">
                        <p class="placeholder-text">Select an option above to see details</p>
                    </div>
                </div>

                <div class="suggestion-box">
                    <h3><i class="fa-regular fa-lightbulb"></i> Have an idea?</h3>
                    <p style="color: #6b6a66; font-size: 0.85rem;">Suggest a topic, game, or event for future polls!</p>
                    <div class="suggestion-input">
                        <input type="text" id="suggestion-input"
                            placeholder="e.g., 'Bowling night' or 'Friendship Bible study'">
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
    <?php endif; ?>
</body>

</html>