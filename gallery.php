<?php
session_start(); 
$is_logged_in = isset($_SESSION['member_id']);
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
    <title>Gonubie Baptist Youth - Gallery</title>
    <link rel="stylesheet" href="css/main.css?v=3.0">
    <link rel="stylesheet" href="css/gallery.css?v=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Blur effect for non-logged in users */
        body:not(.logged-in) .gallery-card img {
            filter: blur(15px);
            transition: filter 0.3s;
        }

        body:not(.logged-in) .gallery-card:hover img {
            filter: blur(7px);
        }

        /* Optional: slight blur on captions too */
        body:not(.logged-in) .gallery-caption {
            filter: blur(3px);
            user-select: none;
        }

        /* Login prompt banner */
        .login-prompt {
            text-align: center;
            padding: 1rem;
            background: #f5f3ee;
            border-radius: 16px;
            margin-bottom: 2rem;
            border: 1px solid #e8e4d9;
        }

        .login-prompt i {
            color: #c9772e;
            margin-right: 0.5rem;
        }

        .login-prompt a {
            color: #c9772e;
            text-decoration: none;
            font-weight: bold;
        }

        .login-prompt button {
            background: #c9772e;
            color: white;
            border: none;
            padding: 0.4rem 1.2rem;
            border-radius: 50px;
            cursor: pointer;
            margin-left: 1rem;
            font-size: 0.85rem;
        }

        .user-status {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            background: #f0ede500;
            padding: 0.4rem 1rem;
            border-radius: 50px;
        }

        .user-status span {
            color: #5a595500;
            font-size: 0.85rem;
        }
    </style>
</head>

<body class="<?php echo $is_logged_in ? 'logged-in' : ''; ?>">

    <?php include __DIR__ . '/inc/header.php'; ?>

    <div class="gallery-page">
        <div class="container">
            <div class="gallery-header">
                <?php if ($is_logged_in): ?>
                    <button id="uploadBtn" class="upload-btn"><i class="fa-solid fa-cloud-upload-alt"></i> Share Your
                        Photo</button>
                <?php else: ?>
                    <div class="login-prompt">
                        <i class="fa-solid fa-lock"></i> Photos are blurred for privacy
                        <a href="login.php?redirect=gallery.php"><button>Login to View</button></a>
                    </div>
                <?php endif; ?>
            </div>
            <div class="masonry-grid" id="masonryGrid">
                <div class="loading">Loading memories...</div>
            </div>
            <button id="loadMoreBtn" class="load-more-btn hidden">Load More Photos</button>
        </div>
    </div>

    <!-- Lightbox Modal for full size img -->
    <div id="lightbox" class="lightbox">
        <span class="lightbox-close">&times;</span>
        <img id="lightbox-img" src="" alt="Full img size">
    </div>

    <input type="file" id="fileInput" accept="image/*" style="display:none">

      <?php include __DIR__ . '/inc/footer.php'; ?>

    <script src="js/gallery.js?v=1.0"></script>
    <script>
        // Pass login status to JavaScript
        const IS_LOGGED_IN = <?php echo $is_logged_in ? 'true' : 'false'; ?>;

        // Prevent lightbox from opening if not logged in
        document.addEventListener('DOMContentLoaded', function () {
            if (!IS_LOGGED_IN) {
                // Override the lightbox click behavior
                document.addEventListener('click', function (e) {
                    const thumb = e.target.closest('.gallery-thumb');
                    if (thumb) {
                        e.preventDefault();
                        e.stopPropagation();
                        window.location.href = 'login.php?redirect=gallery.php';
                        return false;
                    }
                });
            }
        });
    </script>
</body>

</html>