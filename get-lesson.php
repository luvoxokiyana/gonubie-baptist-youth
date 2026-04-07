<?php
header('Content-Type: application/json');
require_once 'database.php';

$db = new Database();
$conn = $db->getConnection();

$stmt = $conn->prepare("SELECT id, title, lesson_date, description, pdf_filename, created_at FROM lessons ORDER BY lesson_date DESC");
$stmt->execute();
$lessons = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(['success' => true, 'lessons' => $lessons]);
?>