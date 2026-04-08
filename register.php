<?php
// session_start(); --> included in header.php
require_once 'database.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $grade = $_POST['grade'] ?? '';
    $parent_phone = trim($_POST['parent_phone'] ?? '');
    
    // Validation
    if (empty($full_name) || empty($username) || empty($password) || empty($grade)) {
        $error = 'Please fill in all required fields';
    } elseif (strlen($password) < 4) {
        $error = 'Password must be at least 4 characters';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match';
    } else {
        $db = new Database();
        $conn = $db->getConnection();
        
        // Check if username exists
        $stmt = $conn->prepare("SELECT id FROM youth_members WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            $error = 'Username already taken. Please try a different name.';
        } else {
            // Create account (pending approval)
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO pending_members (username, password, full_name, grade, parent_phone, request_date) VALUES (?, ?, ?, ?, ?, NOW())");
            
            if ($stmt->execute([$username, $hashed_password, $full_name, $grade, $parent_phone])) {
                $success = 'Account request submitted! A youth leader will approve your account within 24 hours.';
            } else {
                $error = 'Something went wrong. Please try again.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account - Gonubie Baptist Youth</title>
    <link rel="stylesheet" href="css/main.css?v=2.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .register-container {
            max-width: 500px;
            margin: 2rem auto;
            padding: 2rem;
            background: #fff;
            border-radius: 20px;
            border: 1px solid #e8e4d9;
        }
        .register-container h2 {
            color: #c9772e;
            text-align: center;
            margin-bottom: 1.5rem;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #2c2b28;
        }
        .form-group input, .form-group select {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #e8e4d9;
            border-radius: 12px;
            font-size: 1rem;
        }
        .form-group input:focus {
            outline: none;
            border-color: #c9772e;
        }
        .username-preview {
            background: #f0ede5;
            padding: 0.75rem;
            border-radius: 12px;
            font-family: monospace;
            font-size: 1rem;
            margin-top: 0.25rem;
            color: #2c2b28;
            word-break: break-all;
        }
        .username-preview span {
            color: #c9772e;
            font-weight: bold;
        }
        .btn-submit {
            width: 100%;
            padding: 0.75rem;
            background: #c9772e;
            color: white;
            border: none;
            border-radius: 50px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            margin-top: 1rem;
        }
        .error {
            background: #ffe5e5;
            color: #d63031;
            padding: 0.75rem;
            border-radius: 12px;
            margin-bottom: 1rem;
        }
        .success {
            background: #d4f5d4;
            color: #27ae60;
            padding: 0.75rem;
            border-radius: 12px;
            margin-bottom: 1rem;
        }
        .info {
            text-align: center;
            font-size: 0.8rem;
            color: #8a8985;
            margin-top: 1rem;
        }
        .required {
            color: #d63031;
        }
        .hint {
            font-size: 0.7rem;
            color: #8a8985;
            margin-top: 0.25rem;
        }
    </style>
</head>
<body>
    <?php include 'inc/header.php'; ?>
    
    <div class="register-container">
        <h2><i class="fa-solid fa-user-plus"></i> Join GBY</h2>
        
        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        
        <?php if (!$success): ?>
        <form method="POST">
            <div class="form-group">
                <label>Full Name <span class="required">*</span></label>
                <input type="text" id="fullName" name="full_name" required placeholder="e.g., John Doe" autocomplete="off">
            </div>
            
            <div class="form-group">
                <label>Your Username (auto-generated)</label>
                <div class="username-preview" id="usernamePreview">
                    <span>⬤</span> Will appear here
                </div>
                <input type="hidden" id="username" name="username">
                <div class="hint">Your username will be: firstname.lastname</div>
            </div>
            
            <div class="form-group">
                <label>Create Password <span class="required">*</span></label>
                <input type="password" name="password" required placeholder="At least 4 characters">
            </div>
            
            <div class="form-group">
                <label>Confirm Password <span class="required">*</span></label>
                <input type="password" name="confirm_password" required>
            </div>
            
            <div class="form-group">
                <label>Grade <span class="required">*</span></label>
                <select name="grade" required>
                    <option value="">Select Grade</option>
                    <option value="7">Grade 7</option>
                    <option value="8">Grade 8</option>
                    <option value="9">Grade 9</option>
                    <option value="10">Grade 10</option>
                    <option value="11">Grade 11</option>
                    <option value="12">Grade 12</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Parent/Guardian Phone (optional)</label>
                <input type="tel" name="parent_phone" placeholder="For emergency contact">
            </div>
            
            <button type="submit" class="btn-submit">Request Account</button>
        </form>
        
        <div class="info">
            <i class="fa-regular fa-clock"></i> Accounts require leader approval (usually within 24 hours)
        </div>
        <?php endif; ?>
    </div>
    
    <?php include 'inc/footer.php'; ?>
    
    <script>
        // Auto-generate username from full name
        const fullNameInput = document.getElementById('fullName');
        const usernamePreview = document.getElementById('usernamePreview');
        const usernameHidden = document.getElementById('username');
        
        function generateUsername(fullName) {
            // Trim and split by spaces
            const parts = fullName.trim().toLowerCase().split(/\s+/);
            
            if (parts.length === 0) return '';
            if (parts.length === 1) return parts[0];
            
            // First name + last name
            const firstName = parts[0];
            const lastName = parts[parts.length - 1];
            
            // Remove any special characters (keep letters, numbers, dots)
            const cleanFirstName = firstName.replace(/[^a-z0-9]/g, '');
            const cleanLastName = lastName.replace(/[^a-z0-9]/g, '');
            
            return `${cleanFirstName}.${cleanLastName}`;
        }
        
        function updateUsername() {
            const fullName = fullNameInput.value;
            
            if (fullName.trim() === '') {
                usernamePreview.innerHTML = '<span>⬤</span> Will appear here';
                usernameHidden.value = '';
                return;
            }
            
            const username = generateUsername(fullName);
            usernamePreview.innerHTML = `<span>✓</span> ${username}`;
            usernameHidden.value = username;
        }
        
        fullNameInput.addEventListener('input', updateUsername);
    </script>
</body>
</html>