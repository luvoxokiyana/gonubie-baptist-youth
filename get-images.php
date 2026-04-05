<?php
// Folder where your images are stored
$folder = 'images/gallery/';

// Get all image files from the folder
$images = glob($folder . '*.{jpg,jpeg,png,gif,webp,JPG,JPEG,PNG,GIF,WEBP}', GLOB_BRACE);

$galleryImages = [];

foreach ($images as $index => $imagePath) {
    // Get just the filename without extension for caption
    $filename = pathinfo($imagePath, PATHINFO_FILENAME);
    // Replace underscores and hyphens with spaces for better captions
    $caption = str_replace(['_', '-'], ' ', $filename);
    
    // Get image dimensions to help with masonry layout
    list($width, $height) = getimagesize($imagePath);
    
    $galleryImages[] = [
        'id' => $index,
        'src' => $imagePath,
        'caption' => $caption,
        'width' => $width,
        'height' => $height
    ];
}

// Return as JSON
header('Content-Type: application/json');
echo json_encode($galleryImages);
?>