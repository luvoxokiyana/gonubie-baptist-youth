<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gonubie Baptist Youth - Daily Verse</title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/bible-verse.css">
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
            <a href="index.html">home</a>
            <a href="past-lessons.php">past lessons</a>
            <a href="bible-verse.php">bible verse</a>
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

<script src="js/bible-verses.js"></script>    
</body>

</html>