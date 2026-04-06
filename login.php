<?php
session_start();
require_once "database.php";

$error = '';

// Shared password (change this monthly)
$SHARED_PASSWORD = "youth2026"; // Change this monthly and tell youth members

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Check if using shared password first
    if ($password === $SHARED_PASSWORD) {
        // Shared password success - give limited access
        $_SESSION['member_id'] = 0; // Special ID for shared access
        $_SESSION['member_name'] = 'Youth Member';
        $_SESSION['member_username'] = 'shared_access';
        $_SESSION['access_type'] = 'shared';
        
        $redirect = $_GET['redirect'] ?? 'gallery.php';
        header("Location: $redirect");
        exit();
    }
    
    // If not shared password, check individual account in database
    $db = new Database();
    $conn = $db->getConnection();

    $stmt = $conn->prepare('SELECT id, username, password, full_name, role FROM youth_members WHERE username = ?');
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['member_id'] = $user['id'];
        $_SESSION['member_name'] = $user['full_name'];
        $_SESSION['member_username'] = $user['username'];
        $_SESSION['member_role'] = $user['role'] ?? 'member';
        $_SESSION['access_type'] = 'individual';
        
        $redirect = $_GET['redirect'] ?? 'gallery.php';
        header("Location: $redirect");
        exit();
    } else {
        $error = "Invalid username or password. Try the shared password: ";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Youth Login - Gonubie Baptist Youth</title>
    <link rel="stylesheet" href="css/main.css">
    <style>
        .login-container {
            max-width: 450px;
            margin: 100px auto;
            padding: 2rem;
            background: #f5f3ee;
            border-radius: 16px;
            text-align: center;
        }
        .login-container h2 {
            color: #c9772e;
        }
        .login-container input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
        }
        .login-container button {
            background: #c9772e;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            margin-top: 10px;
            font-size: 1rem;
        }
        .error {
            color: red;
            margin-bottom: 1rem;
        }
        .shared-info {
            margin-top: 1.5rem;
            padding: 1rem;
            background: #e8e4d9;
            border-radius: 12px;
            font-size: 0.85rem;
        }
        .shared-info p {
            margin: 5px 0;
        }
        .shared-password {
            font-family: monospace;
            font-size: 1.2rem;
            font-weight: bold;
            color: #c9772e;
        }
        .divider {
            margin: 1.5rem 0;
            position: relative;
            text-align: center;
        }
        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: #ddd;
        }
        .divider span {
            background: #f5f3ee;
            padding: 0 10px;
            position: relative;
            color: #888;
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
            <a href="gallery.php">gallery</a>
        </div>
        <div class="right-container"><i class="fa-solid fa-circle-user"></i></div>
    </div>

    <div class="login-container">
        <h2>🔒 Youth Access</h2>
        <p>Please login to view photos and vote</p>

        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST">
           <input type="text" name="username" placeholder="Username (or anything for shared login)" required>
           <input type="password" name="password" placeholder="Password" required>
           <button type="submit">Login</button>
        </form>

        <div class="divider">
            <span>or</span>
        </div>

        <div class="shared-info">
            <p><strong>📢 Shared Youth Password</strong></p>
            <p>Use any username and the password below:</p>
            <p class="shared-password"><?php echo $SHARED_PASSWORD; ?></p>
            <p style="font-size: 0.75rem; margin-top: 0.5rem;">Contact youth leaders for individual accounts</p>
        </div>

        <div class="info" style="margin-top: 1rem; font-size: 0.8rem;">
            <i class="fa-regular fa-envelope"></i> Contact youth leaders for login help
        </div>
    </div>

    <div class="footer">
        <div class="container">
            <div class="footer-content">
                <p>&copy; 2026 Gonubie Baptist Youth</p>
            </div>
        </div>
    </div>
</body>
</html>