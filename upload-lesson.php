<?php
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

if (!$conn) {
    echo json_encode(['success' => false, 'error' => 'Database connection failed']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit();
}

// Debug: Log what we received
error_log('POST data: ' . print_r($_POST, true));
error_log('FILES data: ' . print_r($_FILES, true));

// Get form data - NOTE: The field names must match what JS sends
$title = isset($_POST['title']) ? trim($_POST['title']) : '';
$date = isset($_POST['date']) ? trim($_POST['date']) : '';
$description = isset($_POST['description']) ? trim($_POST['description']) : '';

// Debug: Log extracted values
error_log("Title: '$title', Date: '$date', Description: '$description'");

if (empty($title)) {
    echo json_encode(['success' => false, 'error' => 'Title is required']);
    exit();
}

if (empty($date)) {
    echo json_encode(['success' => false, 'error' => 'Date is required']);
    exit();
}

if (empty($description)) {
    echo json_encode(['success' => false, 'error' => 'Description is required']);
    exit();
}

// Check if file was uploaded
if (!isset($_FILES['pdf']) || $_FILES['pdf']['error'] !== UPLOAD_ERR_OK) {
    $errorCode = isset($_FILES['pdf']) ? $_FILES['pdf']['error'] : 'No file';
    echo json_encode(['success' => false, 'error' => "PDF upload failed. Code: $errorCode"]);
    exit();
}

// Check file type
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mimeType = finfo_file($finfo, $_FILES['pdf']['tmp_name']);
finfo_close($finfo);

if ($mimeType !== 'application/pdf') {
    echo json_encode(['success' => false, 'error' => 'File must be a PDF. Detected: ' . $mimeType]);
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