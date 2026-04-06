<?php
session_start();
require_once "database.php";

// Only allow logged-in leaders to create accounts
if (!isset($_SESSION['member_role']) || $_SESSION['member_role'] !== 'leader') {
    die('Access denied. Only youth leaders can create accounts.');
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $full_name = $_POST['full_name'];
    $role = $_POST['role'] ?? 'member';
    
    $db = new Database();
    $conn = $db->getConnection();
    
    $stmt = $conn->prepare("INSERT INTO youth_members (username, password, full_name, role) VALUES (?, ?, ?, ?)");
    if ($stmt->execute([$username, $password, $full_name, $role])) {
        $message = "Account created for $full_name!";
    } else {
        $message = "Error: Username may already exist";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Create Youth Account - Admin</title>
    <link rel="stylesheet" href="css/main.css">
    <style>
        body { background: #0d1117; font-family: Arial; padding: 2rem; }
        .form-container { max-width: 500px; margin: 0 auto; background: #161b22; padding: 2rem; border-radius: 16px; color: #fff; }
        input, select { width: 100%; padding: 10px; margin: 10px 0; border-radius: 8px; border: 1px solid #30363d; background: #0d1117; color: #fff; }
        button { background: #f0b90b; border: none; padding: 12px; border-radius: 8px; cursor: pointer; font-weight: bold; width: 100%; }
        .message { padding: 10px; border-radius: 8px; margin-bottom: 1rem; }
        .success { background: #238636; }
        .error { background: #f85149; }
        h2 { color: #f0b90b; }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Create Youth Member Account</h2>
        <p>Create individual accounts for youth members</p>
        
        <?php if ($message): ?>
            <div class="message <?php echo strpos($message, '✅') !== false ? 'success' : 'error'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="text" name="full_name" placeholder="Full Name" required>
            <select name="role">
                <option value="member">Member</option>
                <option value="leader">Youth Leader</option>
            </select>
            <button type="submit">Create Account</button>
        </form>
        
        <p style="margin-top: 1rem; font-size: 0.8rem; text-align: center;">
            <a href="gallery.php" style="color: #f0b90b;">← Back to Gallery</a>
        </p>
    </div>
</body>
</html>