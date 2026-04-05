<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Gonubie Baptist Youth - Gallery</title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/gallery.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <div class="header">
        <div class="left-container"><span><i class="fa-solid fa-cross"></i> Gonubie Baptist Youth</span></div>
        <div class="middle-container">
            <a href="index.html">home</a>
            <a href="past-lessons.php">past lessons</a>
            <a href="bible-verse.php">bible verse</a>
            <a href="voting.php">voting</a>
            <a href="gallery.php" class="active">gallery</a>
        </div>
        <div class="right-container"><i class="fa-solid fa-circle-user"></i></div>
    </div>

    <div class="gallery-page">
        <div class="container">
            <div class="gallery-header">
                <button id="uploadBtn" class="upload-btn"><i class="fa-solid fa-cloud-upload-alt"></i> Share Your
                    Photo</button>
            </div>
            <div class="masonry-grid" id="masonryGrid">
                <div class="loading">📸 Loading memories...</div>
            </div>
        </div>
    </div>

    <input type="file" id="fileInput" accept="image/*" style="display:none">

    <div class="footer">
        <div class="container">
            <p>&copy; 2026 Gonubie Baptist Youth</p>
        </div>
    </div>

<script src="js/gallery.js"></script>
</body>

</html>