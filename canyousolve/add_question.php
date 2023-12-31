<?php
require_once __DIR__ . '/../../config/config.php';
require_once "../vendor/autoload.php";

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
    header('location: index.php');
    exit();
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=yes, initial-scale=1.0">
    <title>Add Questions</title>
    <link rel="stylesheet" media="all" href="../assets/css/admin.css">
</head>
<body class="site">
<header class="nav">

    <input type="checkbox" id="nav-check">


    <div class="nav-btn">
        <label for="nav-check">
            <span></span>
            <span></span>
            <span></span>
        </label>
    </div>

    <div class="nav-links">
        <?php $database->regular_navigation(); ?>
    </div>

    <div class="name-website">
        <h1 class="webtitle">Can You Solve? Add Page</h1>
    </div>

</header>
<main class="main_container">
    <form id="add_to_db_table" class="data-form" method="post" enctype="multipart/form-data">
        <input type="hidden" name="points" value="15">
        <div class="file_grid_area">
            <input id="file" class="file-input-style" type="file" name="image">
            <label for="file">Select file</label>
        </div>
        <select class="select-css" name="category">
            <option disabled>Select a Category</option>
            <option value="wildlife">Wildlife</option>
            <option selected value="lego">LEGO</option>
            <option value="photography">Photography</option>
        </select>
        <div class="question_grid_area">
            <label>Question</label>
            <input class="answer_style" type="text" name="question" value="">
        </div>
        <div class="answer_grid_area">
            <label>Answer</label>
            <input class="answer_style" type="text" name="answer" value="">
        </div>
        <div class="submit_grid_area">
            <button class="button_style" type="submit" name="submit" value="enter">Submit</button>
        </div>
    </form>
</main>
<aside class="sidebar">
    <?php $database->showAdminNavigation(); ?>
</aside>
<footer class="colophon">
    <p>&copy; <?php echo date("Y") ?> Can You Solve?</p>
</footer>
<script src="save_new_questions.js"></script>
</body>
</html>