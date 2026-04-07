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

// Get form data
$title = $_POST['title'] ?? '';
$date = $_POST['date'] ?? '';
$description = $_POST['description'] ?? '';

if (empty($title) || empty($date) || empty($description)) {
    echo json_encode(['success' => false, 'error' => 'Please fill in all fields']);
    exit();
}

// Check if file was uploaded
if (!isset($_FILES['pdf']) || $_FILES['pdf']['error'] !== UPLOAD_ERR_OK) {
    $errorCode = isset($_FILES['pdf']) ? $_FILES['pdf']['error'] : 'No file';
    echo json_encode(['success' => false, 'error' => 'PDF upload failed. Code: ' . $errorCode]);
    exit();
}

// Read the PDF file
$pdfData = file_get_contents($_FILES['pdf']['tmp_name']);
$pdfFilename = $_FILES['pdf']['name'];

// Insert into database
try {
    $stmt = $conn->prepare("INSERT INTO lessons (title, lesson_date, description, pdf_filename, pdf_data) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$title, $date, $description, $pdfFilename, $pdfData]);
    
    echo json_encode(['success' => true, 'message' => 'Lesson uploaded successfully!']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
}
?>