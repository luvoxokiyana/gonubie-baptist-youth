<?php
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $date = $_POST['date'] ?? '';
    $description = $_POST['description'] ?? '';
    
    if (empty($title) || empty($date) || empty($description)) {
        echo json_encode(['success' => false, 'error' => 'Please fill in all fields']);
        exit();
    }
    
    if (!isset($_FILES['pdf']) || $_FILES['pdf']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['success' => false, 'error' => 'Please upload a PDF file']);
        exit();
    }
    
    $pdfData = file_get_contents($_FILES['pdf']['tmp_name']);
    $pdfFilename = $_FILES['pdf']['name'];
    
    $stmt = $conn->prepare("INSERT INTO lessons (title, lesson_date, description, pdf_filename, pdf_data) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$title, $date, $description, $pdfFilename, $pdfData]);
    
    echo json_encode(['success' => true, 'message' => 'Lesson uploaded successfully!']);
    exit();
}

echo json_encode(['success' => false, 'error' => 'Invalid request method']);
?>