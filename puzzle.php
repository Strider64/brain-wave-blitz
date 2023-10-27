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
<html lang="en" itemscope itemtype="http://schema.org/WebPage">
<head>
    <!-- Meta tags for responsiveness -->
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=yes, initial-scale=1.0">
    <title>Connect a Piece</title>
    <link rel="stylesheet" media="all" href="assets/css/puzzle_styling.css">
    <script type="application/ld+json">
        {
            "@context": "http://schema.org",
            "@type": "VideoGame",
            "name": "Connect a Piece",
            "description": "An interactive puzzle game where users can connect pieces to form a complete image.",
            "image": "https://www.brainwaveblitz.com/assets/puzzle_images/img-7e4d33f5b396ec6d9fae4ba09a95197c-600x400.jpeg",
            "url": "https://www.brainwaveblitz.com/puzzle.php",
            "gamePlatform": ["Web"],
            "publisher": "Brain Wave Blitz"
        }
    </script>

</head>
<body class="site">
<header class="headerStyle" itemprop="header">
    <div class="loginStyle">
        <h1 class="intro" itemprop="headline">Brain Wave Blitz - Connect a Piece</h1>
    </div>
</header>
<div class="nav">
    <!-- Input and label for the mobile navigation bar -->
    <input type="checkbox" class="nav-btn" id="nav-btn">
    <label for="nav-btn">
        <span></span>
        <span></span>
        <span></span>
    </label>

    <!-- Navigation links -->
    <nav class="nav-links" id="nav-links" itemprop="breadcrumb">
        <!-- Generating regular navigation links with a method from the Database class -->
        <?php $database->regular_navigation(); ?>
    </nav>
</div>
<main class="main_container" itemprop="mainContentOfPage">
    <canvas id="puzzleCanvas" width="900" height="700"></canvas>
    <div id="customAlertOverlay" class="custom-alert-overlay">
        <div id="customAlert" class="custom-alert">
            <div id="customAlertContent" class="custom-alert-content">
                <p id="alertText">Your custom alert text will appear here.</p>
            </div>
        </div>
    </div>
</main>
<aside class="sidebar">
    <div class="categorySelection">
        <label for="category">Choose a category:</label>
        <select id="category" name="category">
            <option value="general">General</option>
            <option value="halloween">Halloween</option>
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
        <img src="assets/puzzle_images/img-7e4d33f5b396ec6d9fae4ba09a95197c-600x400.jpeg" id="puzzleImage" alt="Puzzle Image">
        <p class="imageDescription">Text</p>
    </div>


</aside>

<footer class="colophon">
    <p>&copy; <?php echo date("Y") ?> Connect a Piece</p>
</footer>
<script>
    if (window.innerWidth <= 768) {
        window.location.href = "https://www.brainwaveblitz.com/brainwaveblitz.php";
    }
</script>
<script src="assets/js/puzzle_script.js"></script>
</body>
</html>

