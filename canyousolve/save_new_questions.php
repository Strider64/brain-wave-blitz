<?php
require_once '../assets/config/config.php';
require_once "../vendor/autoload.php";

use PhotoTech\ErrorHandler;
use PhotoTech\Database;
use PhotoTech\LoginRepository as Login;

$errorHandler = new ErrorHandler();

// Register the exception handler method
set_exception_handler([$errorHandler, 'handleException']);

$database = new Database();
$pdo = $database->createPDO();
$login = new Login($pdo);

// Check if the form was submitted with POST method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the input and textarea values
    $points = $_POST['points'] ?? null;
    $question = $_POST['question'] ?? null;
    $answer = $_POST['answer'] ?? null;

    // Validate input and textarea values as needed
    // ...

    // Check if the file was uploaded successfully
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadedFile = $_FILES['image'];

        // Validate the file (e.g., file size, type, etc.)
        $maxFileSize = 25 * 1024 * 1024; // 25 MB
        $allowedFileTypes = ['image/jpeg', 'image/png'];

        if ($uploadedFile['size'] > $maxFileSize) {
            echo json_encode(['error' => 'File size is too large.']);
            exit();
        }

        if (!in_array($uploadedFile['type'], $allowedFileTypes)) {
            echo json_encode(['error' => 'Invalid file type.']);
            exit();
        }

        // Move the uploaded file to the desired destination
        $destinationDirectory = '../assets/canvas_images/';
        $saveDirectory = '/assets/canvas_images/';
        $destinationFilename = time() . '_' . basename($uploadedFile['name']);
        $destinationPath = $destinationDirectory . $destinationFilename;

        if (move_uploaded_file($uploadedFile['tmp_name'], $destinationPath)) {
            // Load the image
            $image = imagecreatefromjpeg($destinationPath);

            // Get the original dimensions
            $original_width = imagesx($image);
            $original_height = imagesy($image);

            // Calculate the new dimensions
            $new_width = 1200;
            $new_height = 800;
            $scale = min($new_width / $original_width, $new_height / $original_height);
            $width = intval($original_width * $scale);
            $height = intval($original_height * $scale);

            // Create a new image with the new dimensions
            $new_image = imagecreatetruecolor($width, $height);

            // Copy and resize the original image to the new image
            imagecopyresampled($new_image, $image, 0, 0, 0, 0, $width, $height, $original_width, $original_height);
            // Save the new image
            imagejpeg($new_image, $destinationPath, 100);

            $savePath = $saveDirectory . $destinationFilename;
            // Prepare the SQL INSERT statement
            $sql = "INSERT INTO bird_trivia (points, question, answer, canvas_images) VALUES (:points, :question, :answer, :canvas_images)";
            $stmt = $pdo->prepare($sql);

            // Bind the values to the placeholders
            $stmt->bindValue(':points', $points, PDO::PARAM_INT);
            $stmt->bindValue(':question', $question);
            $stmt->bindValue(':answer', $answer);
            $stmt->bindValue(':canvas_images', $savePath);

            // Execute the prepared statement
            $insertSuccess = $stmt->execute();

            echo json_encode(['success' => true, 'message' => 'Form data processed successfully.', 'file_path' => $destinationPath]);
        } else {
            echo json_encode(['error' => 'Failed to move uploaded file.']);
        }
    } else {
        echo json_encode(['error' => 'File upload failed.']);
    }
} else {
    echo json_encode(['error' => 'Invalid request method.']);
}


