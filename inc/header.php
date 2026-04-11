<?php
// inc/header.php - Reusable header with dropdown menu
session_start();
$is_logged_in = isset($_SESSION['member_id']);
$member_name = $_SESSION['member_name'] ?? '';
$member_role = $_SESSION['member_role'] ?? '';
?>
<!--Hello World-->
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
        <div class="profile-dropdown" id="profileDropdown">
            <div class="dropdown-trigger">
                <i class="fa-solid fa-circle-user"></i>
            </div>
            <div class="dropdown-menu">
                <?php if ($is_logged_in): ?>
                    <div class="dropdown-user-info">
                        <div class="user-name"><?php echo htmlspecialchars($member_name); ?></div>
                        <div class="user-role"><?php echo htmlspecialchars($member_role); ?></div>
                    </div>
                    <div class="dropdown-divider"></div>
                    
                    <!-- Show Admin Panel link only for leaders -->
                    <?php if ($member_role === 'leader'): ?>
                        <a href="/admin-approvals.php"><i class="fa-solid fa-user-check"></i> Admin Panel</a>
                    <?php endif; ?>
                    
                    <a href="/faq.php"><i class="fa-regular fa-circle-question"></i> FAQ</a>
                    <button id="logoutBtn"><i class="fa-solid fa-sign-out-alt"></i> Logout</button>
                <?php else: ?>
                    <a href="login.php?redirect=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>">
                        <i class="fa-solid fa-sign-in-alt"></i> Login
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
// Profile dropdown functionality - with mobile touch support
document.addEventListener('DOMContentLoaded', function() {
    const dropdown = document.getElementById('profileDropdown');
    const trigger = document.querySelector('.dropdown-trigger');
    const body = document.body;
    
    // Make the trigger area larger and more touch-friendly
    if (trigger) {
        // Add a larger tap area
        trigger.style.minWidth = '44px';
        trigger.style.minHeight = '44px';
        trigger.style.display = 'flex';
        trigger.style.alignItems = 'center';
        trigger.style.justifyContent = 'center';
        
        // Handle both click and touch events
        trigger.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            dropdown.classList.toggle('active');
            if (dropdown.classList.contains('active')) {
                body.classList.add('dropdown-open');
            } else {
                body.classList.remove('dropdown-open');
            }
        });
        
        // Also handle touchstart for better mobile response
        trigger.addEventListener('touchstart', function(e) {
            e.preventDefault();
            e.stopPropagation();
            dropdown.classList.toggle('active');
            if (dropdown.classList.contains('active')) {
                body.classList.add('dropdown-open');
            } else {
                body.classList.remove('dropdown-open');
            }
        }, { passive: false });
    }
    
    // Close dropdown when clicking/tapping outside
    document.addEventListener('click', function(e) {
        if (dropdown && !dropdown.contains(e.target)) {
            dropdown.classList.remove('active');
            body.classList.remove('dropdown-open');
        }
    });
    
    document.addEventListener('touchstart', function(e) {
        if (dropdown && !dropdown.contains(e.target)) {
            dropdown.classList.remove('active');
            body.classList.remove('dropdown-open');
        }
    });
    
    // Close on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && dropdown && dropdown.classList.contains('active')) {
            dropdown.classList.remove('active');
            body.classList.remove('dropdown-open');
        }
    });
    
    const logoutBtn = document.getElementById('logoutBtn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', function() {
            window.location.href = 'logout.php';
        });
        logoutBtn.addEventListener('touchstart', function(e) {
            e.preventDefault();
            window.location.href = 'logout.php';
        });
    }
});
</script>
