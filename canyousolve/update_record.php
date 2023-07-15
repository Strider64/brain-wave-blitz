<?php
// edit_update_record.php

header('Content-Type: application/json');
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
try {
    // Check if the required form data is provided
    if (!isset($_POST['id']) || !isset($_POST['question']) || !isset($_POST['answer'])) {
        throw new Exception("Missing required form data.");
    }

    // Get form data
    $id = (int) $_POST['id'];
    $category = $_POST['category'];
    $question= $_POST['question'];
    $answer = $_POST['answer'];
    $points = 15;

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['size'] > 0) {
        $uploadedFile = $_FILES['image'];

        // Validate the file (e.g., file size, type, etc.)
        $maxFileSize = 56 * 1024 * 1024; // 50 MB
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




        // Load the image
        $loadedImage = Image::make($uploadedFile['tmp_name']);

        // Resize the image
        $loadedImage->resize(2048, 1365, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        // Save the new image
        $loadedImage->save($destinationPath, 100);

        // Retrieve the current image file path from the database
        $sql1 = "SELECT canvas_images FROM canyousolve WHERE id = :id";
        $stmt1 = $pdo->prepare($sql1);
        $stmt1->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt1->execute();
        $row = $stmt1->fetch(PDO::FETCH_ASSOC);
        $current_image_path = $row['canvas_images'];

        // Delete the old image if it exists
        $current_image_abs_path = $_SERVER['DOCUMENT_ROOT'] . $current_image_path;
        if (file_exists($current_image_abs_path)) {
            unlink($current_image_abs_path);
        }

        // Prepare the SQL query with placeholders
        $sql = "UPDATE canyousolve SET category = :category, question = :question, answer = :answer, canvas_images = :canvas_images, points = :points WHERE id = :id";
        $stmt = $pdo->prepare($sql);

        // Bind the values to the placeholders
        $savePath = $saveDirectory . $destinationFilename;
        $stmt->bindParam(':canvas_images', $savePath);

    } else {
        // Prepare the SQL query with placeholders
        $sql = "UPDATE canyousolve SET category = :category, question = :question, answer = :answer, points = :points WHERE id = :id";
        $stmt = $pdo->prepare($sql);
    }

    // Bind the values to the placeholders
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->bindParam(':category', $category);
    $stmt->bindParam(':question', $question);
    $stmt->bindParam(':answer', $answer);
    $stmt->bindParam(':points', $points);

    // Execute the prepared statement
    $stmt->execute();

    // Check if the update was successful
    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'Record updated successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'No record updated.']);
    }
} catch (PDOException $e) {
    echo json_encode(['PDO error' => $e->getMessage()]);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}

