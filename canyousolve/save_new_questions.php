<?php
require_once __DIR__ . '/../../config/config.php';
require_once "../vendor/autoload.php";

use brainwave\ErrorHandler;
use brainwave\Database;
use brainwave\LoginRepository as Login;
use Intervention\Image\ImageManagerStatic as Image;

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

// Check if the form was submitted with POST method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the input and textarea values
    $category = $_POST['category'] ?? null;
    $question = $_POST['question'] ?? null;
    $answer = $_POST['answer'] ?? null;
    $points = $_POST['points'] ?? null;
    // Validate input and textarea values as needed
    // ...

    // Check if the file was uploaded successfully
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadedFile = $_FILES['image'];

        // Validate the file (e.g., file size, type, etc.)
        $maxFileSize = 56 * 1024 * 1024; // 25 MB
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
            $image = Image::make($destinationPath);

            // Resize the image to a width of 1200 and constrain aspect ratio (auto height)
            $image->resize(1200, null, function ($constraint) {
                $constraint->aspectRatio();
            });

            // Save the resized image
            $image->save($destinationPath);

            $savePath = $saveDirectory . $destinationFilename;

            // Prepare the SQL INSERT statement
            $sql = "INSERT INTO canyousolve (points, question, answer, canvas_images, category) VALUES (:points, :question, :answer, :canvas_images, :category)";
            $stmt = $pdo->prepare($sql);

            // Bind the values to the placeholders
            $stmt->bindValue(':points', $points, PDO::PARAM_INT);
            $stmt->bindValue(':question', $question);
            $stmt->bindValue(':answer', $answer);
            $stmt->bindValue(':canvas_images', $savePath);
            $stmt->bindParam(':category', $category);
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
