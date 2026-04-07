<?php
session_start();
require_once 'database.php';

// Optional: Require login to view lessons
// if (!isset($_SESSION['member_id'])) {
//     header('HTTP/1.0 403 Forbidden');
//     exit('Please login to view lessons');
// }

$lessonId = $_GET['id'] ?? 0;

$db = new Database();
$conn = $db->getConnection();

$stmt = $conn->prepare("SELECT pdf_filepath, pdf_filename FROM lessons WHERE id = ?");
$stmt->execute([$lessonId]);
$lesson = $stmt->fetch(PDO::FETCH_ASSOC);

if ($lesson && $lesson['pdf_filepath'] && file_exists($lesson['pdf_filepath'])) {
    $filePath = $lesson['pdf_filepath'];
    $filename = $lesson['pdf_filename'];
    
    header('Content-Type: application/pdf');
    header('Content-Disposition: inline; filename="' . $filename . '"');
    readfile($filePath);
} else {
    header('HTTP/1.0 404 Not Found');
    echo 'PDF not found';
}
?>