<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once 'database.php';

$db = new Database();
$db->setupTables();
$conn = $db->getConnection();

if (!$conn) {
    echo json_encode(['success' => false, 'error' => 'Database connection failed']);
    exit();
}

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
                break;
            }
            
            $result = castVote($conn, $pollType, $optionId, $user_ip);
            echo json_encode($result);
            break;

        case 'get_suggestions':
            $stmt = $conn->prepare("SELECT suggestion_text FROM suggestions ORDER BY created_at DESC LIMIT 50");
            $stmt->execute();
            $suggestions = $stmt->fetchAll(PDO::FETCH_COLUMN);
            echo json_encode(['success' => true, 'data' => $suggestions]);
            break;

        case 'add_suggestion':
            $input = json_decode(file_get_contents('php://input'), true);
            $text = trim($input['suggestion'] ?? '');
            
            if (empty($text)) {
                echo json_encode(['success' => false, 'error' => 'Suggestion cannot be empty']);
                break;
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
    $stmt = $conn->prepare("SELECT id FROM user_vote_status WHERE user_ip = ? AND poll_type = ?");
    $stmt->execute([$user_ip, $pollType]);
    if ($stmt->fetch()) {
        return ['success' => false, 'error' => 'You have already voted on this poll'];
    }
    
    $stmt = $conn->prepare("SELECT option_id FROM poll_options WHERE poll_type = ? AND option_id = ?");
    $stmt->execute([$pollType, $optionId]);
    if (!$stmt->fetch()) {
        return ['success' => false, 'error' => 'Invalid option'];
    }
    
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