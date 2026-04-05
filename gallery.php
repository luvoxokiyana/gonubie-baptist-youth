<?php
session_start();
$is_logged_in = isset($_SESSION['member_id']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Gonubie Baptist Youth - Gallery</title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/gallery.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Additional styles for blur protection - add to existing CSS */
        .gallery-card.protected {
            position: relative;
        }
        
        .gallery-card.protected .gallery-thumb {
            filter: blur(20px);
            transition: filter 0.3s;
        }
        
        .gallery-card.protected .gallery-caption {
            filter: blur(4px);
        }
        
        .blur-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.7);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: white;
            text-align: center;
            z-index: 10;
            border-radius: 16px;
            cursor: pointer;
        }
        
        .blur-overlay i {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }
        
        .blur-overlay span {
            font-size: 0.8rem;
            background: #c9772e;
            padding: 0.4rem 1rem;
            border-radius: 50px;
            margin-top: 0.5rem;
        }
        
        .blur-overlay:hover span {
            background: #b15f1e;
        }
        
        .login-prompt {
            text-align: center;
            padding: 2rem;
            background: #f5f3ee;
            border-radius: 16px;
            margin-bottom: 2rem;
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
            padding: 0.5rem 1.5rem;
            border-radius: 50px;
            cursor: pointer;
            margin-left: 1rem;
        }
        
        .user-status {
            display: flex;
            align-items: center;
            gap: 1rem;
            background: #f0ede5;
            padding: 0.5rem 1rem;
            border-radius: 50px;
        }
        
        .user-status span {
            color: #5a5955;
        }
        
        .user-status a {
            color: #c9772e;
            text-decoration: none;
        }
        
        @media (max-width: 700px) {
            .blur-overlay i {
                font-size: 1.5rem;
            }
            .blur-overlay span {
                font-size: 0.7rem;
                padding: 0.3rem 0.8rem;
            }
        }
    </style>
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
        <div class="right-container">
            <?php if ($is_logged_in): ?>
                <div class="user-status">
                    <span><i class="fa-solid fa-user-check"></i> <?php echo htmlspecialchars($_SESSION['member_name'] ?? 'Member'); ?></span>
                    <a href="logout.php"><i class="fa-solid fa-sign-out-alt"></i></a>
                </div>
            <?php else: ?>
                <a href="login.php?redirect=gallery.php" style="color: #c9772e;">
                    <i class="fa-solid fa-circle-user"></i> Login
                </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="gallery-page">
        <div class="container">
            <div class="gallery-header">
                <?php if ($is_logged_in): ?>
                    <button id="uploadBtn" class="upload-btn"><i class="fa-solid fa-cloud-upload-alt"></i> Share Your Photo</button>
                <?php else: ?>
                    <div class="login-prompt">
                        <i class="fa-solid fa-lock"></i> Photos are blurred for privacy
                        <a href="login.php?redirect=gallery.php"><button>Login to View Photos</button></a>
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

    <script src="js/gallery.js"></script>
    <script>
        // Pass login status to JavaScript
        const IS_LOGGED_IN = <?php echo $is_logged_in ? 'true' : 'false'; ?>;
        
        // Override the image click behavior in gallery.js
        document.addEventListener('DOMContentLoaded', function() {
            // Wait for gallery to load, then add protection
            setTimeout(function() {
                protectImages();
            }, 500);
        });
        
        function protectImages() {
            if (IS_LOGGED_IN) return;
            
            // Find all gallery cards and add blur protection
            const cards = document.querySelectorAll('.gallery-card');
            cards.forEach(card => {
                if (!card.classList.contains('protected')) {
                    card.classList.add('protected');
                    
                    // Add click-to-login overlay
                    const img = card.querySelector('img');
                    if (img && !card.querySelector('.blur-overlay')) {
                        const overlay = document.createElement('div');
                        overlay.className = 'blur-overlay';
                        overlay.innerHTML = `
                            <i class="fa-solid fa-lock"></i>
                            <span>Login to view photo</span>
                        `;
                        overlay.onclick = function(e) {
                            e.stopPropagation();
                            window.location.href = 'login.php?redirect=gallery.php';
                        };
                        card.style.position = 'relative';
                        card.appendChild(overlay);
                    }
                }
            });
        }
        
        // Monitor for dynamically loaded images (load more)
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.addedNodes.length) {
                    protectImages();
                }
            });
        });
        
        observer.observe(document.getElementById('masonryGrid'), { childList: true, subtree: true });
    </script>
</body>

</html>