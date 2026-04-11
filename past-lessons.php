<?php
//session_start(); included in header.php
$is_leader = isset($_SESSION['member_role']) && $_SESSION['member_role'] === 'leader';
?>
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
    <title>Gonubie Baptist Youth - Past Lessons</title>
    <link rel="stylesheet" href="css/main.css?v=3.0">
    <link rel="stylesheet" href="css/past-lessons.css?v=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .upload-section {
            background: #161b22;
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            border: 1px solid #30363d;
        }

        .upload-section h3 {
            color: #f0b90b;
            margin-bottom: 1rem;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #e6edf3;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 0.75rem;
            background: #0d1117;
            border: 1px solid #30363d;
            border-radius: 8px;
            color: #e6edf3;
        }

        .upload-submit {
            background: #f0b90b;
            color: #0d1117;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
        }

        .leader-only-badge {
            background: #f0b90b;
            color: #0d1117;
            padding: 0.25rem 0.75rem;
            border-radius: 50px;
            font-size: 0.7rem;
            font-weight: bold;
        }
    </style>
</head>

<body>
   <?php include __DIR__ . '/inc/header.php'; ?>

    <div class="lessons-page">
        <div class="container">
            <div class="lessons-header">
                <h1><i class="fa-solid fa-chalkboard-user"></i> Past Lessons</h1>
                <p>Catch up on messages you missed or review your favorites</p>
            </div>

            <!-- Upload Section - ONLY SHOWN TO LEADERS -->
            <?php if ($is_leader): ?>
                <div class="upload-section" id="uploadSection">
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
                            <textarea id="lessonDesc" rows="3"
                                placeholder="Brief summary, key takeaways, or main Bible verses..."></textarea>
                        </div>
                        <div class="form-group">
                            <label>Upload PDF Slideshow</label>
                            <input type="file" id="lessonPDF" accept="application/pdf">
                            <small style="color: #8b949e;">Upload the PDF slideshow from the lesson</small>
                        </div>
                        <button class="upload-submit" id="uploadLessonBtn"><i class="fa-solid fa-upload"></i> Upload
                            Lesson</button>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Lessons Grid -->
            <div class="lessons-grid" id="lessonsGrid">
                <div class="empty-lessons">
                    <i class="fa-regular fa-folder-open"></i>
                    <p>Loading lessons...</p>
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

     <?php include __DIR__ . '/inc/footer.php'; ?>

    <script src="js/past-lessons.js?v=1.0"></script>
    <script>
        // Pass PHP variables to JavaScript
        const IS_LEADER = <?php echo $is_leader ? 'true' : 'false'; ?>;
    </script>
</body>

</html>