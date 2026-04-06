<?php
session_start();
require_once "database.php";

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $bd = new Database();
    $conn = $bd->getConnection();

    $stmt = $conn->prepare('SELECT id, username, password, full_name FROM youth_members WHERE  username = ?');
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['member_id'] = $user['id'];
        $_SESSION['member_name'] = $user['full_name'];
        $_SESSION['member_username'] = $user['username'];

        //Redirect user back to gallery page
        $redirect = $_GET['redirect'] ?? 'gallery.php';
        header("Location: $redirect");
        exit();
    } else {
        $error = "Invalid username and password";
    }

}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Youth Login - Gonubie Baptist Church</title>
    <link rel="stylesheet" href="css/main.css">
     <style>
        .login-container {
            max-width: 400px;
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
        }
        .login-container button {
            background: #c9772e;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            margin-top: 10px;
        }
        .error {
            color: red;
            margin-bottom: 1rem;
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
        <h2>Youth Gallery Access</h2>
        <p>Please log in to view the youth even photos</p>

        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST">
           <input type="text" name="username" placeholder="Username" required>
           <input type="password" name="password" placeholder="Password" required>
           <button type="submit">Login to View Photos</button> 
        </form>
         <div class="info">
            Contact youth leaders for login credentials
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