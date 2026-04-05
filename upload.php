<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    $uploadDir = 'images/gallery/';
    
    // Generate unique filename
    $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    $filename = time() . '_' . uniqid() . '.' . $extension;
    $destination = $uploadDir . $filename;
    
    // Move uploaded file
    if (move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
        echo json_encode(['success' => true, 'path' => $destination]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Upload failed']);
    }
}
?>