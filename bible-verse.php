<!DOCTYPE html>
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


    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gonubie Baptist Youth - Daily Verse</title>
    <link rel="stylesheet" href="css/main.css?v=1.0">
    <link rel="stylesheet" href="css/bible-verse.css?v=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
            <a href="bible-verse.php" class="active">bible verse</a>
            <a href="voting.php">voting</a>
            <a href="gallery.php">gallery</a>
        </div>
        <div class="right-container">
            <i class="fa-solid fa-circle-user"></i>
        </div>
    </div>

    <div class="verse-page">

        <div class="verse-card" id="verseCard">
            <div class="verse-icon">
                <i class="fa-solid fa-quote-right"></i>
            </div>
            <div class="verse-text" id="verseText">Loading...</div>
            <div class="verse-reference" id="verseReference"></div>
            <div class="verse-date" id="verseDate"></div>

            <div class="share-buttons">
                <button class="share-btn" id="shareWhatsApp"><i class="fa-brands fa-whatsapp"></i> WhatsApp</button>
                <button class="share-btn" id="shareCopy"><i class="fa-regular fa-copy"></i> Copy Verse</button>
            </div>

            <div class="verse-context" id="verseContext" style="display: none;">
                <h4><i class="fa-regular fa-lightbulb"></i> Context & Reflection</h4>
                <p id="contextText"></p>
            </div>
        </div>

        <div class="container">
            <div class="calendar-section">
                <h3><i class="fa-regular fa-calendar-alt"></i> This Week's Verses</h3>
                <div class="calendar-grid" id="weekVerses"></div>
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

    <script src="js/bible-verses.js?v=1.0"></script>
</body>

</html>