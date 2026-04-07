<?php
header('Content-Type: application/json');
require_once 'database.php';

$db = new Database();
$conn = $db->getConnection();

if (!$conn) {
    echo json_encode(['success' => false, 'error' => 'Database connection failed']);
    exit();
}

try {
    // Don't select pdf_data anymore - just the file path
    $stmt = $conn->prepare("SELECT id, title, lesson_date, description, pdf_filename, pdf_filepath, created_at FROM lessons ORDER BY lesson_date DESC");
    $stmt->execute();
    $lessons = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['success' => true, 'lessons' => $lessons]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>