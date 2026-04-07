<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['member_role']) || $_SESSION['member_role'] !== 'leader') {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

require_once 'database.php';

$db = new Database();
$conn = $db->getConnection();

if (!$conn) {
    echo json_encode(['success' => false, 'error' => 'Database connection failed']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(['success' => false, 'error' => 'No JSON data received']);
    exit();
}

$title = trim($input['title'] ?? '');
$date = trim($input['date'] ?? '');
$description = trim($input['description'] ?? '');
$pdfFilename = $input['pdf_filename'] ?? '';
$pdfBase64 = $input['pdf_data'] ?? '';

if (empty($title) || empty($date) || empty($description) || empty($pdfBase64)) {
    echo json_encode(['success' => false, 'error' => 'Missing required fields']);
    exit();
}

// Create uploads folder if it doesn't exist
$uploadDir = __DIR__ . '/uploads/lessons/';
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Generate unique filename
$uniqueFilename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $pdfFilename);
$filePath = 'uploads/lessons/' . $uniqueFilename;
$fullPath = $uploadDir . $uniqueFilename;

// Save PDF to file
$pdfData = base64_decode($pdfBase64);
if (file_put_contents($fullPath, $pdfData) === false) {
    echo json_encode(['success' => false, 'error' => 'Failed to save PDF file']);
    exit();
}

// Save only the file path to database (not the PDF data)
try {
    $stmt = $conn->prepare("INSERT INTO lessons (title, lesson_date, description, pdf_filename, pdf_filepath) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$title, $date, $description, $pdfFilename, $filePath]);
    echo json_encode(['success' => true, 'message' => 'Lesson uploaded successfully!']);
} catch (PDOException $e) {
    // Delete the file if database insert fails
    unlink($fullPath);
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
}
?>