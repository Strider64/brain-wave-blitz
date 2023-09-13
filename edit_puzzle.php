<?php
require_once __DIR__ . '/../config/config.php';
require_once "vendor/autoload.php";

use Intervention\Image\ImageManagerStatic as Image;
use brainwave\{
    ErrorHandler,
    Database,
    LoginRepository as Login,
    ImageContentManager
};

$errorHandler = new ErrorHandler();

// Register the exception handler method
set_exception_handler([$errorHandler, 'handleException']);

$database = new Database();
$pdo = $database->createPDO();

// Check to see user is login in, if not redirect them to the home or login in page:
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
    <title>Edit Jigsaw Puzzle</title>
    <link rel="stylesheet" media="all" href="assets/css/puzzle_styling.css">
</head>
<body class="site">
<header class="nav">
    <!-- Input and label for the mobile navigation bar -->
    <input type="checkbox" class="nav-btn" id="nav-btn">
    <label for="nav-btn">
        <span></span>
        <span></span>
        <span></span>
    </label>

    <!-- Navigation links -->
    <nav class="nav-links" id="nav-links">
        <!-- Generating regular navigation links with a method from the Database class -->
        <?php $database->regular_navigation(); ?>
    </nav>

    <!-- Website name -->
    <div class="name-website">
        <h1 class="webtitle">Edit Jigsaw Puzzle</h1>
    </div>
</header>
<main class="main_container">
    <form id="data_entry_form" class="checkStyle" method="post" enctype="multipart/form-data">
        <input id="id" type="hidden" name="id"  value="">
        <div id="image_display_area">
            <img id="image_for_edited_record" src="" alt="">
        </div>
        <div id="file_grid_area">
            <input id="file" class="file-input-style" type="file" name="image">
            <label for="file">Select file</label>
        </div>
        <div id="description_grid_area">
            <label class="text_label_style" for="description">Description</label>
            <textarea class="text_input_style" id="description" name="description"></textarea>
        </div>
        <label id="select_grid_difficulty_level_area">
            <select id="difficulty_level" class="select-css" name="difficulty_level">
                <option value="Easy">Easy</option>
                <option value="Medium">Medium</option>
                <option value="Hard">Hard</option>
                <option value="Expert">Expert</option>
            </select>
        </label>
        <label id="select_grid_category_area">
            <select id="category" class="select-css" name="category">
                <option value="lego">LEGO</option>
                <option value="wildlife">Wildlife</option>
            </select>
        </label>
        <div id="submit_picture_grid_area">
            <button class="form-button" type="submit" name="submit" value="enter">Submit</button>
        </div>
    </form>
</main>
<aside class="sidebar">
    <div class="search-form-container">
        <form id="searchForm">
            <div class="input-group">
                <label for="searchTerm">Search:</label>
                <input type="text" placeholder="Search Content" id="searchTerm" class="input-field" autofocus required>
            </div>
            <button class="search_button" type="submit">Search</button>
        </form>
    </div>
    <?php $database->showAdminNavigation(); ?>
</aside>

<footer class="colophon">
    <p>&copy; <?php echo date("Y") ?> Edit Jigsaw Puzzle</p>
</footer>
<script src="assets/js/edit_puzzle.js"></script>
</body>
</html>


