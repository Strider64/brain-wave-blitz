<?php
require_once __DIR__ . '/../../config/config.php';
require_once "../vendor/autoload.php";

use brainwave\ErrorHandler;
use brainwave\Database;

$errorHandler = new ErrorHandler();

// Register the exception handler method
set_exception_handler([$errorHandler, 'handleException']);

$database = new Database();
$pdo = $database->createPDO();
$data = json_decode(file_get_contents('php://input'), true);
if (!$data) {
    errorOutput('Invalid input data', 400);
    exit();
}

// Fetch the word from the database
$stmt = $pdo->prepare('SELECT canvas_images FROM canyousolve WHERE id=:id LIMIT 1');
$stmt->execute(['id' => (int)$data['id']]);
$result = $stmt->fetch(PDO::FETCH_ASSOC);

if ($result) {
    output(['image' => $result['canvas_images']]);
} else {
    // Reached the end of the table
    output(['end_of_table' => true]);
}

function errorOutput($output, $code = 500): void
{
    http_response_code($code);
    echo json_encode(['error' => $output]);
}

function output($data): void
{
    http_response_code(200);
    echo json_encode($data);
}
