<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gonubie Baptist Youth - Past Lessons</title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/past-lessons.css">
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
            <a href="past-lessons.php" class="active">past lessons</a>
            <a href="bible-verse.php">bible verse</a>
            <a href="gallery.php">gallery</a>
            <a href="voting.php">voting</a>
        </div>
        <div class="right-container">
            <i class="fa-solid fa-circle-user"></i>
        </div>
    </div>

    <div class="lessons-page">
        <div class="container">
            <div class="lessons-header">
                <h1><i class="fa-solid fa-chalkboard-user"></i> Past Lessons</h1>
                <p>Catch up on messages you missed or review your favorites</p>
            </div>

            <!-- Upload Section (Admin only - hidden by default) -->
            <div class="upload-section" id="uploadSection" style="display: none;">
                <h3><i class="fa-solid fa-cloud-upload-alt"></i> Upload New Lesson (PDF)</h3>
                <div class="upload-form">
                    <div class="form-group">
                        <label>Lesson Title</label>
                        <input type="text" id="lessonTitle" placeholder="e.g., Finding Your Purpose">
                    </div>
                    <div class="form-group">
                        <label>Date</label>
                        <input type="date" id="lessonDate">
                    </div>
                    <div class="form-group">
                        <label>Description / Key Verses</label>
                        <textarea id="lessonDesc"
                            placeholder="Brief summary, key takeaways, or main Bible verses..."></textarea>
                    </div>
                    <div class="form-group">
                        <label>Upload PDF Slideshow</label>
                        <input type="file" id="lessonPDF" accept="application/pdf">
                        <small style="color: #6e7a76;">Upload the PDF slideshow from the lesson</small>
                    </div>
                    <button class="upload-submit" id="uploadLessonBtn"><i class="fa-solid fa-upload"></i> Upload
                        Lesson</button>
                </div>
            </div>

            <!-- Lessons Grid -->
            <div class="lessons-grid" id="lessonsGrid">
                <div class="empty-lessons">
                    <i class="fa-regular fa-folder-open"></i>
                    <p>No lessons uploaded yet. Check back soon!</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for PDF viewer -->
    <div id="pdfModal" class="modal">
        <div class="modal-content">
            <span class="close-modal">&times;</span>
            <h3 class="modal-title" id="modalTitle"></h3>
            <div class="pdf-container" id="pdfContainer">
                <!-- PDF embed will go here -->
            </div>
            <div style="text-align: center;">
                <button class="download-pdf-btn" id="downloadPdfBtn"><i class="fa-solid fa-download"></i> Download
                    PDF</button>
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
                    <i class="fa-brands fa-discord"></i>
                    <i class="fa-solid fa-envelope"></i>
                </div>
            </div>
        </div>
    </div>

    <script src="js/past-lessons.js"></script>
</body>

</html>