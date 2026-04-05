<?php
// database.php - Database connection and setup
class Database {
    private $host = 'sql100.infinityfree.com';
    private $db_name = 'church_youth';
    private $username = 'if0_41572398';
    private $password = 'GuyGam3r20';
    private $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec("set names utf8");
        } catch(PDOException $exception) {
            error_log("Connection error: " . $exception->getMessage());
        }
        return $this->conn;
    }

    public function setupTables() {
        $conn = $this->getConnection();
        if (!$conn) return false;

        $sql = "
            CREATE TABLE IF NOT EXISTS votes (
                id INT AUTO_INCREMENT PRIMARY KEY,
                poll_type VARCHAR(20) NOT NULL,
                option_id VARCHAR(10) NOT NULL,
                user_ip VARCHAR(45) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                UNIQUE KEY unique_vote (poll_type, user_ip)
            );

            CREATE TABLE IF NOT EXISTS poll_options (
                id INT AUTO_INCREMENT PRIMARY KEY,
                poll_type VARCHAR(20) NOT NULL,
                option_id VARCHAR(10) NOT NULL,
                option_name VARCHAR(100) NOT NULL,
                display_order INT DEFAULT 0,
                UNIQUE KEY unique_option (poll_type, option_id)
            );

            CREATE TABLE IF NOT EXISTS suggestions (
                id INT AUTO_INCREMENT PRIMARY KEY,
                suggestion_text TEXT NOT NULL,
                user_ip VARCHAR(45) NOT NULL,
                status VARCHAR(20) DEFAULT 'pending',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            );

            CREATE TABLE IF NOT EXISTS user_vote_status (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_ip VARCHAR(45) NOT NULL,
                poll_type VARCHAR(20) NOT NULL,
                voted_option_id VARCHAR(10) NOT NULL,
                voted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                UNIQUE KEY unique_user_poll (user_ip, poll_type)
            );
        ";

        try {
            $conn->exec($sql);
            $this->insertDefaultOptions($conn);
            return true;
        } catch(PDOException $e) {
            error_log("Setup error: " . $e->getMessage());
            return false;
        }
    }

    private function insertDefaultOptions($conn) {
        $defaults = [
            'bible' => [
                ['b1', 'Overcoming Anxiety', 1],
                ['b2', 'Friendship & Faith', 2],
                ['b3', 'Purpose & Calling', 3],
                ['b4', 'Social Media & Identity', 4]
            ],
            'game' => [
                ['g1', 'Capture the Flag (Glow)', 1],
                ['g2', 'Minute to Win It', 2],
                ['g3', 'Karaoke Battle', 3],
                ['g4', 'Escape Room Challenge', 4]
            ],
            'event' => [
                ['e1', 'Beach Braai & Bonfire', 1],
                ['e2', 'Lock-in (All Night)', 2],
                ['e3', 'Movie & Popcorn Night', 3],
                ['e4', 'Sports Tournament', 4]
            ]
        ];

        foreach ($defaults as $pollType => $options) {
            $stmt = $conn->prepare("INSERT IGNORE INTO poll_options (poll_type, option_id, option_name, display_order) VALUES (?, ?, ?, ?)");
            foreach ($options as $opt) {
                $stmt->execute([$pollType, $opt[0], $opt[1], $opt[2]]);
            }
        }
    }
}

// api.php - REST API endpoint for voting
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once 'database.php';
$db = new Database();
$db->setupTables();
$conn = $db->getConnection();

function getUserIP() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) return $_SERVER['HTTP_CLIENT_IP'];
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) return $_SERVER['HTTP_X_FORWARDED_FOR'];
    return $_SERVER['REMOTE_ADDR'];
}

$user_ip = getUserIP();
$action = $_GET['action'] ?? '';

try {
    switch($action) {
        case 'get_votes':
            $pollType = $_GET['poll'] ?? '';
            if ($pollType) {
                $data = getPollData($conn, $pollType, $user_ip);
                echo json_encode(['success' => true, 'data' => $data]);
            } else {
                $result = [];
                foreach (['bible', 'game', 'event'] as $type) {
                    $result[$type] = getPollData($conn, $type, $user_ip);
                }
                echo json_encode(['success' => true, 'data' => $result]);
            }
            break;

        case 'cast_vote':
            $input = json_decode(file_get_contents('php://input'), true);
            $pollType = $input['poll_type'] ?? '';
            $optionId = $input['option_id'] ?? '';
            
            if (!$pollType || !$optionId) {
                echo json_encode(['success' => false, 'error' => 'Missing parameters']);
                exit();
            }
            
            $result = castVote($conn, $pollType, $optionId, $user_ip);
            echo json_encode($result);
            break;

        case 'get_suggestions':
            $stmt = $conn->prepare("SELECT suggestion_text, created_at FROM suggestions WHERE status = 'approved' ORDER BY created_at DESC LIMIT 50");
            $stmt->execute();
            $suggestions = $stmt->fetchAll(PDO::FETCH_COLUMN);
            echo json_encode(['success' => true, 'data' => $suggestions]);
            break;

        case 'add_suggestion':
            $input = json_decode(file_get_contents('php://input'), true);
            $text = trim($input['suggestion'] ?? '');
            
            if (empty($text)) {
                echo json_encode(['success' => false, 'error' => 'Suggestion cannot be empty']);
                exit();
            }
            
            $stmt = $conn->prepare("INSERT INTO suggestions (suggestion_text, user_ip) VALUES (?, ?)");
            $stmt->execute([$text, $user_ip]);
            echo json_encode(['success' => true, 'message' => 'Suggestion added!']);
            break;

        default:
            echo json_encode(['success' => false, 'error' => 'Invalid action']);
    }
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
}

function getPollData($conn, $pollType, $user_ip) {
    // Get options with vote counts
    $stmt = $conn->prepare("
        SELECT po.option_id, po.option_name, COUNT(v.id) as votes
        FROM poll_options po
        LEFT JOIN votes v ON po.poll_type = v.poll_type AND po.option_id = v.option_id
        WHERE po.poll_type = ?
        GROUP BY po.option_id, po.option_name
        ORDER BY po.display_order
    ");
    $stmt->execute([$pollType]);
    $options = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Check if user has voted
    $stmt = $conn->prepare("SELECT voted_option_id FROM user_vote_status WHERE user_ip = ? AND poll_type = ?");
    $stmt->execute([$user_ip, $pollType]);
    $userVote = $stmt->fetch(PDO::FETCH_ASSOC);
    
    return [
        'options' => $options,
        'has_voted' => $userVote !== false,
        'user_choice' => $userVote ? $userVote['voted_option_id'] : null
    ];
}

function castVote($conn, $pollType, $optionId, $user_ip) {
    // Check if already voted
    $stmt = $conn->prepare("SELECT id FROM user_vote_status WHERE user_ip = ? AND poll_type = ?");
    $stmt->execute([$user_ip, $pollType]);
    if ($stmt->fetch()) {
        return ['success' => false, 'error' => 'You have already voted on this poll'];
    }
    
    // Verify option exists
    $stmt = $conn->prepare("SELECT option_id FROM poll_options WHERE poll_type = ? AND option_id = ?");
    $stmt->execute([$pollType, $optionId]);
    if (!$stmt->fetch()) {
        return ['success' => false, 'error' => 'Invalid option'];
    }
    
    // Record vote
    $conn->beginTransaction();
    try {
        $stmt = $conn->prepare("INSERT INTO votes (poll_type, option_id, user_ip) VALUES (?, ?, ?)");
        $stmt->execute([$pollType, $optionId, $user_ip]);
        
        $stmt = $conn->prepare("INSERT INTO user_vote_status (user_ip, poll_type, voted_option_id) VALUES (?, ?, ?)");
        $stmt->execute([$user_ip, $pollType, $optionId]);
        
        $conn->commit();
        return ['success' => true, 'message' => 'Vote recorded!'];
    } catch(Exception $e) {
        $conn->rollBack();
        return ['success' => false, 'error' => 'Failed to record vote'];
    }
}
?>

<!-- voting.php - Updated with backend integration -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gonubie Baptist Youth - Voting</title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/voting.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="header">
        <div class="left-container">
            <span><i class="fa-solid fa-cross"></i> Gonubie Baptist Youth</span>
        </div>
        <div class="middle-container">
            <a href="home.html">home</a>
            <a href="past-lessons.php">past lessons</a>
            <a href="bible-verse.php">bible verse</a>
            <a href="voting.php">voting</a>
            <a href="gallery.php">gallery</a>
        </div>
        <div class="right-container">
            <i class="fa-solid fa-circle-user"></i>
        </div>
    </div>

    <div class="voting-page">
        <div class="container">
            <div class="voting-header">
                <h1><i class="fa-solid fa-square-poll-vertical"></i> Youth Votes</h1>
                <p>Your voice matters! Vote for what you want to see at youth.</p>
            </div>

            <div class="poll-grid">
                <div class="poll-card" id="poll-bible">
                    <h2><i class="fa-solid fa-bible"></i> Next Bible Study</h2>
                    <div class="poll-description">What topic should we study next Friday?</div>
                    <div class="poll-options" id="bible-options"></div>
                    <button class="vote-btn" id="vote-bible-btn">🗳️ Cast Vote</button>
                    <div class="results" id="bible-results"></div>
                    <div id="bible-voted-message"></div>
                </div>

                <div class="poll-card" id="poll-game">
                    <h2><i class="fa-solid fa-gamepad"></i> Next Game</h2>
                    <div class="poll-description">Which game do you want to play?</div>
                    <div class="poll-options" id="game-options"></div>
                    <button class="vote-btn" id="vote-game-btn">🎮 Cast Vote</button>
                    <div class="results" id="game-results"></div>
                    <div id="game-voted-message"></div>
                </div>

                <div class="poll-card" id="poll-event">
                    <h2><i class="fa-regular fa-calendar"></i> Future Event</h2>
                    <div class="poll-description">What event should we plan next month?</div>
                    <div class="poll-options" id="event-options"></div>
                    <button class="vote-btn" id="vote-event-btn">📅 Cast Vote</button>
                    <div class="results" id="event-results"></div>
                    <div id="event-voted-message"></div>
                </div>
            </div>

            <div class="suggestion-box">
                <h3><i class="fa-regular fa-lightbulb"></i> Have an idea?</h3>
                <p style="color: #8b949e; font-size: 0.85rem;">Suggest a topic, game, or event for future polls!</p>
                <div class="suggestion-input">
                    <input type="text" id="suggestion-input" placeholder="e.g., 'Bowling night' or 'Friendship Bible study'">
                    <button id="submit-suggestion"><i class="fa-regular fa-paper-plane"></i> Suggest</button>
                </div>
                <div class="suggestions-list" id="suggestions-list"></div>
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
                    <i class="fa-solid fa-envelope"></i>
                </div>
            </div>
        </div>
    </div>

    <script src="js/voting.js"></script>
</body>
</html>