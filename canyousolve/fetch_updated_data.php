<?php

// search_records.php

// Send a response to the client with the content type set to application/json.
header('Content-Type: application/json');

// Include the necessary files and classes for this script.
require_once __DIR__ . '/../../config/config.php';
require_once "../vendor/autoload.php";

// Use some classes for error handling, database connection and login-related actions.
use brainwave\ErrorHandler;
use brainwave\Database;
use brainwave\LoginRepository as Login;

$errorHandler = new ErrorHandler();

// Register the exception handler method
set_exception_handler([$errorHandler, 'handleException']);

$database = new Database();
$pdo = $database->createPDO();
$login = new Login($pdo);
if (!$login->check_login_token()) {
    // Send a 401 Unauthorized status code and a JSON error message
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$sql = "SELECT id, question FROM canyousolve";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// If results were found, return them to the client as JSON.
if ($data) {
    echo json_encode($data);
} else {
    echo json_encode(['message' => 'No data found.']);
}