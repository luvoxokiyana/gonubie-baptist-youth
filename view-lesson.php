<?php
session_start();
require_once 'database.php';

$lessonId = $_GET['id'] ?? 0;

$db = new Database();
$conn = $db->getConnection();

$stmt = $conn->prepare("SELECT pdf_data, pdf_filename FROM lessons WHERE id = ?");
$stmt->execute([$lessonId]);
$lesson = $stmt->fetch(PDO::FETCH_ASSOC);

if ($lesson && $lesson['pdf_data']) {
    header('Content-Type: application/pdf');
    header('Content-Disposition: inline; filename="' . $lesson['pdf_filename'] . '"');
    echo $lesson['pdf_data'];
} else {
    header('HTTP/1.0 404 Not Found');
    echo 'PDF not found';
}
?>