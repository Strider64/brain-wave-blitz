<?php
// Include the configuration file and autoload file from the composer.
require_once __DIR__ . '/../config/config.php';
require_once "vendor/autoload.php";

/*
 * Jigsaw Puzzle 1.0 βeta
 * Created by John Pepp
 * on August 16, 2023
 * Updated by John Pepp
 * on August 16, 2023
 */

// Import the ErrorHandler and Database classes from the PhotoTech namespace.
use brainwave\{
    ErrorHandler,
    Database,
    Links,
    ImageContentManager,
    LoginRepository as Login
};

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
    <title>Jigsaw Puzzle</title>
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
        <h1 class="webtitle">Jigsaw Puzzle</h1>
    </div>
</header>
<main class="main_container">
    <canvas id="puzzleCanvas" width="900" height="700"></canvas>
</main>
<aside class="sidebar">

</aside>

<footer class="colophon">
    <p>&copy; <?php echo date("Y") ?> Brain Wave Blitz</p>
</footer>
<script src="assets/js/puzzle_script.js"></script>
</body>
</html>
