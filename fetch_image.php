<?php
// Include the configuration file and autoload file from the composer.
require_once __DIR__ . '/../config/config.php';
require_once "vendor/autoload.php";

use brainwave\ErrorHandler;
use brainwave\Database;


$errorHandler = new ErrorHandler();

// Register the exception handler method
set_exception_handler([$errorHandler, 'handleException']);

$database = new Database();
$pdo = $database->createPDO();

if (!isset($_SESSION['shown_images'])) {
    $_SESSION['shown_images'] = [];
}

$placeholders = implode(',', array_fill(0, count($_SESSION['shown_images']), '?'));
$query = "SELECT image_path FROM puzzle_images";

if ($placeholders) {
    $query .= " WHERE image_path NOT IN ($placeholders)";
}

$query .= " ORDER BY RAND() LIMIT 1";
$stmt = $pdo->prepare($query);
$stmt->execute($_SESSION['shown_images']);

$row = $stmt->fetch();

// If there are no more images left to show, handle accordingly.
if (!$row) {
    // Reset session or handle accordingly
    $_SESSION['shown_images'] = [];
    echo 'NO_MORE_IMAGES';
} else {
    $_SESSION['shown_images'][] = $row['image_path'];
    echo $row['image_path'];
}


