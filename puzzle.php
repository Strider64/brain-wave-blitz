<?php
// Include the configuration file and autoload file from the composer.
require_once __DIR__ . '/../config/config.php';
require_once "vendor/autoload.php";

/*
 * Jigsaw Puzzle 2.0 βeta
 * Created by John Pepp
 * on August 16, 2023
 * Updated by John Pepp
 * on September 23, 2023
 */

// Import the ErrorHandler and Database classes from the PhotoTech namespace.
use brainwave\{
    ErrorHandler,
    Database,
    Links,
    ImageContentManager,
    LoginRepository as Login
};

$_SESSION['shown_images'] = []; // Start the game over if web browser is refreshed

// Instantiate the ErrorHandler class
$errorHandler = new ErrorHandler();

// Set the exception handler to use the handleException method from the ErrorHandler instance
set_exception_handler([$errorHandler, 'handleException']);

// Create a new instance of the Database class
$database = new Database();
// Create a PDO instance using the Database class's method
$pdo = $database->createPDO();
?>
<!doctype html>
<html lang="en">
<head>
    <!-- Meta tags for responsiveness -->
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=yes, initial-scale=1.0">
    <title>Connect a Piece</title>
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
        <h1 class="webtitle">Connect a Piece</h1>
    </div>
</header>
<main class="main_container">
    <canvas id="puzzleCanvas" width="900" height="700"></canvas>
</main>
<aside class="sidebar">
    <div class="categorySelection">
        <label for="category">Choose a category:</label>
        <select id="category" name="category">
            <option value="lego">LEGO</option>
            <option value="wildlife" selected>Wildlife</option>
        </select>
    </div>
    <div class="titleSelection">
        <label for="title">Choose Puzzle</label>
        <select id="title" name="title">

        </select>
    </div>
    <div class="puzzleImage">
        <img id="puzzleImage" alt="Puzzle Image" />
        <p class="imageDescription">Text</p>
    </div>

    <div id="customAlertOverlay" class="custom-alert-overlay">
        <div id="customAlert" class="custom-alert">
            <div id="customAlertContent" class="custom-alert-content">
                <p id="alertText">Your custom alert text will appear here.</p>
            </div>
        </div>
    </div>
</aside>

<footer class="colophon">
    <p>&copy; <?php echo date("Y") ?> Connect a Piece</p>
</footer>
<script src="assets/js/puzzle_script.js"></script>
</body>
</html>

