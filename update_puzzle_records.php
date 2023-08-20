<?php
// edit_update_puzzle.php

header('Content-Type: application/json');
require_once __DIR__ . '/../config/config.php';
require_once "vendor/autoload.php";

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

$id = (int) $_POST['id'];
$description = $_POST['description'];
$difficulty_level = $_POST['difficulty_level'];
$category = $_POST['category'];

try {
// Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['size'] > 0) {

        $errors = array();
        $exif_data = [];
        $file_name = $_FILES['image']['name']; // Temporary file:
        $file_size = $_FILES['image']['size'];
        $file_tmp = $_FILES['image']['tmp_name'];
        $file_type = $_FILES['image']['type'];
        $file_ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));


        $extensions = array("jpeg", "jpg", "png");

        if (in_array($file_ext, $extensions, true) === false) {
            $errors[] = "extension not allowed, please choose a JPEG or PNG file.";
        }

        if ($file_size >= 58720256) {
            $errors[] = 'File size must be less than or equal to 42 MB';
        }

        /*
         * Create unique name for image.
         */
        $image_random_string = bin2hex(random_bytes(16));
        $image_path = 'assets/puzzle_images/img-' . $image_random_string . '-600x400' . '.' . $file_ext;


        move_uploaded_file($file_tmp, $image_path);


        // Load the image
        $image = Image::make($image_path);

        // Resize the image
        $image->resize(600, 400, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        // Sharpening the image
        $image->sharpen(25);

        // Save the new image
        $image->save($image_path, 100);


        $sql = "UPDATE puzzle_images SET image_path = :image_path = :image_path, description = :description, difficulty_level = :difficulty_level, category = :category WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':image_path', $image_path);
    } else {
        // Prepare the SQL query with placeholders
        $sql = "UPDATE puzzle_images SET description = :description, difficulty_level = :difficulty_level, category = :category WHERE id = :id";
        $stmt = $pdo->prepare($sql);
    }





    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':difficulty_level', $difficulty_level);
    $stmt->bindParam(':category', $category);

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



