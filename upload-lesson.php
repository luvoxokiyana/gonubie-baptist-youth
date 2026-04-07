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

// Read JSON input (NOT $_POST)
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

if (empty($pdfBase64)) {
    echo json_encode(['success' => false, 'error' => 'PDF data is required']);
    exit();
}

$pdfData = base64_decode($pdfBase64);

try {
    $stmt = $conn->prepare("INSERT INTO lessons (title, lesson_date, description, pdf_filename, pdf_data) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$title, $date, $description, $pdfFilename, $pdfData]);
    echo json_encode(['success' => true, 'message' => 'Lesson uploaded successfully!']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
}
?>