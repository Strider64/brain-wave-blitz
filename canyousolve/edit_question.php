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
    header('location: ../index.php');
    exit();
}

$sql = "SELECT id, question FROM canyousolve";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$records = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=yes, initial-scale=1.0">
    <title>Edit - Can You See</title>
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
        <h1 class="webtitle">Can You Solve? Edit Page</h1>
    </div>

</div>
<main class="main_container">
    <form id="data_entry_form" class="checkStyle" action="edit_question.php" method="post" enctype="multipart/form-data">

        <input id="id" type="hidden" name="id" value="">
        <input type="hidden" name="user_id" value="<?= $_SESSION['user_id'] ?>">
        <input type="hidden" name="author" value="John Pepp>">
        <input type="hidden" name="page" value="gallery">
        <input type="hidden" name="action" value="upload">
        <div id="image_display_area">
            <img id="image_for_edited_record" src="" alt="">
        </div>
        <div id="file_grid_area">
            <input id="file" class="file-input-style" type="file" name="image">
            <label for="file">Select file</label>
        </div>
        <label id="select_grid_category_area">
            <select class="select-css" name="category">
                <option id="category" value=""></option>
                <option value="wildlife">Wildlife</option>
                <option value="lego">LEGO</option>
                <option value="photography">Photography</option>
            </select>
        </label>
        <div id="heading_heading_grid_area">
            <label class="heading_label_style" for="heading">Question</label>
            <input class="heading answer_style" type="text" name="question" value="" tabindex="1" required>
        </div>
        <div id="content_style_grid_area">
            <label class="text_label_style" for="content">Answer</label>
            <input id="content" class="answer_style" name="answer" tabindex="2">
        </div>
        <div id="submit_picture_grid_area">
            <button class="form-button" type="submit" name="submit" value="enter">submit</button>
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
            <div class="input-group">
                <label for="select_id">Question:</label>
                <select class="select-css" id="select_id" name="id">
                    <option value="" disabled selected>Select Question</option>
                    <?php
                    foreach ( $records as $record) {
                            echo '<option value="' . $record['id'] . '">' . $record['question'] . '</option>';
                        }
                    ?>
                    ?>
                </select>
            </div>
            <button class="search_button" type="submit">Search</button>
        </form>
    </div>
    <?php $database->showAdminNavigation(); ?>
</aside>
<footer class="colophon">
    <p>&copy; <?php echo date("Y") ?> Brain Wave Blitz</p>
</footer>
<script src="edit_can_you_solve_records.js"></script>
</body>
</html>