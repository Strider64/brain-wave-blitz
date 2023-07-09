<?php
require_once __DIR__ . '/../../config/config.php';
require_once "../vendor/autoload.php";
$response = [
    'score' => $_SESSION['score'] ?? 0,
];

header('Content-Type: application/json');
echo json_encode($response);
?>