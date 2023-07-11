<?php
require_once __DIR__ . '/../../config/config.php';
require_once "../vendor/autoload.php";

use brainwave\ErrorHandler;
use brainwave\Database;
use brainwave\LoginRepository as Login;

$errorHandler = new ErrorHandler();

// Register the exception handler method
set_exception_handler([$errorHandler, 'handleException']);

$database = new Database();
$pdo = $database->createPDO();

// Write a SQL query to fetch the image paths
$sql = "SELECT canvas_images FROM canyousolve";

// Execute the query
$stmt = $pdo->query($sql);

// Fetch the results as an associative array
$imagePaths = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Convert the image paths to a JSON string and echo it
echo json_encode($imagePaths);

