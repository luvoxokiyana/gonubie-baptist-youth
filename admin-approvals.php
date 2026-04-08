<?php
session_start();
if (!isset($_SESSION['member_role']) || $_SESSION['member_role'] !== 'leader') {
    header('Location: login.php');
    exit();
}
require_once 'database.php';

$db = new Database();
$conn = $db->getConnection();

// Approve request
if (isset($_GET['approve'])) {
    $id = $_GET['approve'];
    
    // Get pending request
    $stmt = $conn->prepare("SELECT * FROM pending_members WHERE id = ? AND status = 'pending'");
    $stmt->execute([$id]);
    $pending = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($pending) {
        // Insert into youth_members
        $stmt = $conn->prepare("INSERT INTO youth_members (username, password, full_name, role) VALUES (?, ?, ?, 'member')");
        $stmt->execute([$pending['username'], $pending['password'], $pending['full_name']]);
        
        // Update pending status
        $stmt = $conn->prepare("UPDATE pending_members SET status = 'approved', approved_by = ?, approved_date = NOW() WHERE id = ?");
        $stmt->execute([$_SESSION['member_id'], $id]);
        
        $message = "✅ Account approved for " . htmlspecialchars($pending['full_name']);
    }
}

// Decline request
if (isset($_GET['decline'])) {
    $id = $_GET['decline'];
    $stmt = $conn->prepare("UPDATE pending_members SET status = 'declined' WHERE id = ?");
    $stmt->execute([$id]);
    $message = "❌ Request declined";
}

// Get all pending requests
$stmt = $conn->prepare("SELECT * FROM pending_members WHERE status = 'pending' ORDER BY request_date DESC");
$stmt->execute();
$pending = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pending Approvals - Admin</title>
    <link rel="stylesheet" href="css/main.css?v=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .admin-container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
        }
        .pending-card {
            background: #fff;
            border-radius: 16px;
            padding: 1rem;
            margin-bottom: 1rem;
            border: 1px solid #e8e4d9;
        }
        .btn-approve {
            background: #27ae60;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            cursor: pointer;
        }
        .btn-decline {
            background: #d63031;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            cursor: pointer;
        }
        .message {
            padding: 1rem;
            border-radius: 12px;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <?php include 'inc/header.php'; ?>
    
    <div class="admin-container">
        <h2><i class="fa-solid fa-user-check"></i> Pending Account Requests</h2>
        
        <?php if (isset($message)): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <?php if (count($pending) === 0): ?>
            <p>No pending requests. Check back later.</p>
        <?php else: ?>
            <?php foreach ($pending as $request): ?>
                <div class="pending-card">
                    <p><strong><?php echo htmlspecialchars($request['full_name']); ?></strong></p>
                    <p>Username: <?php echo htmlspecialchars($request['username']); ?></p>
                    <p>Grade: <?php echo htmlspecialchars($request['grade']); ?></p>
                    <p>Requested: <?php echo date('M j, Y', strtotime($request['request_date'])); ?></p>
                    <a href="?approve=<?php echo $request['id']; ?>" class="btn-approve"> Approve</a>
                    <a href="?decline=<?php echo $request['id']; ?>" class="btn-decline"> Decline</a>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    
    <?php include 'inc/footer.php'; ?>
</body>
</html>