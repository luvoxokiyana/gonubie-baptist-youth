<?php
// Turn on error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
header('Content-Type: application/json');

// Check if user is logged in AND has leader role
if (!isset($_SESSION['member_role']) || $_SESSION['member_role'] !== 'leader') {
    echo json_encode(['success' => false, 'error' => 'Unauthorized. Only youth leaders can upload lessons.']);
    exit();
}

require_once 'database.php';

$db = new Database();
$conn = $db->getConnection();
$conn->exec("SET GLOBAL max_allowed_packet=67108864"); // 64MB

if (!$conn) {
    echo json_encode(['success' => false, 'error' => 'Database connection failed']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method. Use POST.']);
    exit();
}

// Check if fields are set
$title = $_POST['title'] ?? '';
$date = $_POST['date'] ?? '';
$description = $_POST['description'] ?? '';

if (empty($title) || empty($date) || empty($description)) {
    echo json_encode(['success' => false, 'error' => 'Please fill in all fields']);
    exit();
}

// Check if file was uploaded
if (!isset($_FILES['pdf']) || $_FILES['pdf']['error'] !== UPLOAD_ERR_OK) {
    $uploadError = isset($_FILES['pdf']) ? $_FILES['pdf']['error'] : 'No file';
    echo json_encode(['success' => false, 'error' => 'PDF upload failed. Error code: ' . $uploadError]);
    exit();
}

// Check file type
$fileType = mime_content_type($_FILES['pdf']['tmp_name']);
if ($fileType !== 'application/pdf') {
    echo json_encode(['success' => false, 'error' => 'File must be a PDF. Detected type: ' . $fileType]);
    exit();
}

// Read PDF file
$pdfData = file_get_contents($_FILES['pdf']['tmp_name']);
if ($pdfData === false) {
    echo json_encode(['success' => false, 'error' => 'Failed to read PDF file']);
    exit();
}

$pdfFilename = $_FILES['pdf']['name'];

// Insert into database
try {
    $stmt = $conn->prepare("INSERT INTO lessons (title, lesson_date, description, pdf_filename, pdf_data) VALUES (?, ?, ?, ?, ?)");
    $result = $stmt->execute([$title, $date, $description, $pdfFilename, $pdfData]);
    
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Lesson uploaded successfully!']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Database insert failed']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
}
?>