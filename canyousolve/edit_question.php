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
    <title>Edit Questions</title>
    <link rel="stylesheet" media="all" href="../assets/css/admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
</head>
<body class="site">
<header class="headerStyle">


</header>
<div class="nav">

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
        <h1 class="webtitle">The Photo Tech Guru</h1>
    </div>

</div>
<main class="main_container">
    <div class="home_article">
        <form id="edit_record" method="post" enctype="multipart/form-data">
            <input id="id" type="hidden" name="id" value="">
            <input type="hidden" name="points" value="15">
            <div class="file_grid_area">
                <input id="file" class="file-input-style" type="file" name="image">
                <label for="file">Select file</label>
            </div>
            <div class="question_grid_area">
                <label for="question_style">Question</label>
                <textarea id="question_style" name="question" tabindex=""></textarea>
            </div>
            <div class="answer_grid_area">
                <label for="answer_style">Answer</label>
                <input id="answer_style" type="text" name="answer" value="">
            </div>
            <div class="submit_grid_area">
                <button class="button_style" type="submit" name="submit" value="enter">submit</button>
            </div>
        </form>
    </div>
    <div class="home_sidebar">
        <div class="search-form-container">
            <form id="searchForm">
                <div class="input-group">
                    <label for="searchTerm">Search:</label>
                    <input type="text" placeholder="Search Content" id="searchTerm" class="input-field" autofocus required>
                </div>
                <div class="input-group">
                    <label for="heading">Heading:</label>
                    <select class="select-css" id="heading" name="heading">
                        <option value="" disabled selected>Select Heading</option>
                        <?php
                        foreach ($records as $record) {
                            echo '<option value="' . $record['heading'] . '">' . $record['heading'] . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <button class="search_button" type="submit">Search</button>
            </form>
        </div>
        <?php $database->showAdminNavigation(); ?>
    </div>

</main>
<aside class="sidebar">

</aside>
<footer class="colophon">
    <p>&copy; <?php echo date("Y") ?> Brain Wave Blitz</p>
</footer>
<script src="update_record.js"></script>
</body>
</html>