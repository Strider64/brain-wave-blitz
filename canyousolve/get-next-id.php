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

// Parse the input data
$data = json_decode(file_get_contents('php://input'), true);
if (!$data) {
    try {
        errorOutput('Invalid input data', 400);
    } catch (JsonException $e) {
    }
    exit();
}

$current_id = (int)$data['current_id'];


$stmt = $pdo->prepare('SELECT id, canvas_images FROM canyousolve WHERE id > :current_id ORDER BY id LIMIT 1');
$stmt->bindValue(':current_id', $current_id, PDO::PARAM_INT);
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);

if ($result) {
    // Makes it, so we don't have to decode the json coming from javascript
    header('Content-type: application/json');
    $data = ['next_id' => $result['id'], 'image' => $result['canvas_images']]; //canvas_images is the path
    output($data);
} else {
    // Reached the end of the table
    output(['end_of_table' => true]);
}


function errorOutput($output, $code = 500)
{
    http_response_code($code);
    echo json_encode(['error' => $output]);
}

function output($data)
{
    http_response_code(200);
    echo json_encode($data);
}
