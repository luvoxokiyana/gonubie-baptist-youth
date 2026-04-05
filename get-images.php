<?php
header('Content-Type: application/json');

$folder = 'images/gallery/';
$thumbFolder = 'images/gallery/thumbs/';

// Create thumbs folder if it doesn't exist
if (!file_exists($thumbFolder)) {
    mkdir($thumbFolder, 0777, true);
}

$images = glob($folder . '*.{jpg,jpeg,png,gif,webp,JPG,JPEG,PNG,GIF,WEBP}', GLOB_BRACE);

$galleryImages = [];

foreach ($images as $index => $imagePath) {
    $filename = pathinfo($imagePath, PATHINFO_FILENAME);
    $caption = str_replace(['_', '-'], ' ', $filename);
    list($width, $height) = getimagesize($imagePath);
    
    // Generate thumbnail path
    $thumbPath = $thumbFolder . $filename . '.jpg';
    
    // Create thumbnail if it doesn't exist
    if (!file_exists($thumbPath)) {
        createThumbnail($imagePath, $thumbPath, 300);
    }
    
    $galleryImages[] = [
        'id' => filemtime($imagePath), // Use file modified time for sorting
        'src' => $imagePath,
        'thumb' => file_exists($thumbPath) ? $thumbPath : $imagePath,
        'caption' => $caption,
        'width' => $width,
        'height' => $height,
        'thumbHeight' => 300 * ($height / $width) // Approximate thumbnail height
    ];
}

// Sort by ID (newest first - using file modification time)
usort($galleryImages, function($a, $b) {
    return $b['id'] - $a['id'];
});

echo json_encode($galleryImages);

// Thumbnail creation function
function createThumbnail($source, $destination, $maxWidth) {
    list($width, $height) = getimagesize($source);
    $ratio = $height / $width;
    $newWidth = $maxWidth;
    $newHeight = $maxWidth * $ratio;
    
    $thumb = imagecreatetruecolor($newWidth, $newHeight);
    
    $extension = strtolower(pathinfo($source, PATHINFO_EXTENSION));
    switch($extension) {
        case 'jpg':
        case 'jpeg':
            $sourceImg = imagecreatefromjpeg($source);
            break;
        case 'png':
            $sourceImg = imagecreatefrompng($source);
            break;
        case 'gif':
            $sourceImg = imagecreatefromgif($source);
            break;
        case 'webp':
            $sourceImg = imagecreatefromwebp($source);
            break;
        default:
            return false;
    }
    
    imagecopyresampled($thumb, $sourceImg, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
    imagejpeg($thumb, $destination, 85);
    imagedestroy($thumb);
    imagedestroy($sourceImg);
    return true;
}
?>