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

$category = $data['selected'];

$stmt = $pdo->prepare('SELECT id, canvas_images FROM canyousolve WHERE category = :category ORDER BY id LIMIT 1');
$stmt->execute([":category" => $category]);
$result = $stmt->fetch(PDO::FETCH_ASSOC);

header('Content-type: application/json');
$data = ['first_id' => $result['id'], 'image' => $result['canvas_images']];
output($data);


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
