<?php
header('Content-Type: application/json');
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

try {
    // Check if the required form data is provided
    if (!isset($_POST['id']) || !isset($_POST['question']) || !isset($_POST['answer'])) {
        throw new Exception("Missing required form data.");
    }

    // Get form data
    $id = (int) $_POST['id'];
    $question = $_POST['question'];
    $answer = $_POST['answer'];

// ...
// Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['size'] > 0) {
        $image = $_FILES['image'];
        $destinationDirectory = '../assets/canvas_images/';
        $saveDirectory = '/assets/canvas_images/';
        $destinationFilename = time() . '_' . basename($image['name']);
        $destinationPath = $destinationDirectory . $destinationFilename;
        $target_file = $destinationPath;

        // Get original image dimensions
        list($original_width, $original_height) = getimagesize($image['tmp_name']);

        // Calculate the new dimensions
        $new_width = 1200;
        $new_height = 800;
        $scale = min($new_width / $original_width, $new_height / $original_height);
        $width = intval($original_width * $scale);
        $height = intval($original_height * $scale);

        // Create a new image with the new dimensions
        $resized_image = imagecreatetruecolor($width, $height);

        // Detect image type and create image from uploaded file
        $image_type = exif_imagetype($image['tmp_name']);
        $source_image = match ($image_type) {
            IMAGETYPE_JPEG => imagecreatefromjpeg($image['tmp_name']),
            IMAGETYPE_PNG => imagecreatefrompng($image['tmp_name']),
            IMAGETYPE_GIF => imagecreatefromgif($image['tmp_name']),
            default => throw new Exception('Unsupported image type.'),
        };
        // Retrieve the current image file path from the database
        $current_image_query = "SELECT canvas_images FROM bird_trivia WHERE id = :id";
        $current_image_stmt = $pdo->prepare($current_image_query);
        $current_image_stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $current_image_stmt->execute();
        $current_image_row = $current_image_stmt->fetch(PDO::FETCH_ASSOC);
        $current_image_path = $current_image_row['canvas_images'];

        // Delete the old image if it exists
        $current_image_abs_path = $_SERVER['DOCUMENT_ROOT'] . $current_image_path;
        if (file_exists($current_image_abs_path)) {
            unlink($current_image_abs_path);
        }
        // Resize the image
        imagecopyresampled($resized_image, $source_image, 0, 0, 0, 0, $width, $height, $original_width, $original_height);

        // Save the resized image to the target file
        switch ($image_type) {
            case IMAGETYPE_JPEG:
                imagejpeg($resized_image, $target_file);
                break;
            case IMAGETYPE_PNG:
                imagepng($resized_image, $target_file);
                break;
            case IMAGETYPE_GIF:
                imagegif($resized_image, $target_file);
                break;
        }

        // Free up memory
        imagedestroy($source_image);
        imagedestroy($resized_image);

        move_uploaded_file($image['tmp_name'], $target_file);

        // Prepare the SQL query with placeholders
        $sql = "UPDATE bird_trivia SET question = :question, answer = :answer, canvas_images = :canvas_images WHERE id = :id";
        $stmt = $pdo->prepare($sql);

        // Bind the values to the placeholders
        $savePath = $saveDirectory . $destinationFilename;
        $stmt->bindParam(':canvas_images', $savePath);
    } else {
        // Prepare the SQL query with placeholders
        $sql = "UPDATE bird_trivia SET question = :question, answer = :answer WHERE id = :id";
        $stmt = $pdo->prepare($sql);
    }

    // Bind the values to the placeholders
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->bindParam(':question', $question);
    $stmt->bindParam(':answer', $answer);

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
