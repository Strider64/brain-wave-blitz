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
$sql = "UPDATE score SET score=:score WHERE id=:id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['score' => 0, 'id' => 1]);
$login = new Login($pdo);
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=yes, initial-scale=1.0">
    <title>Can You Solve?</title>
    <link rel="stylesheet" media="all" href="../assets/css/canyousolve.css">
    <style>

    </style>
    <script>
        let initialScore = <?php echo isset($_SESSION['score']) ? $_SESSION['score'] : 0; ?>;
        let isLoggedIn = <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;
    </script>

    <script type="module" src="split_picture.js"></script>
    <script type="module" src="load_image_onto_canvas.js"></script>
</head>
<body class="site">


<header class="nav">
    <input type="checkbox" class="nav-btn" id="nav-btn">
    <label for="nav-btn">
        <span></span>
        <span></span>
        <span></span>
    </label>

    <nav class="nav-links" id="nav-links">
        <?php $database->regular_navigation(); ?>
    </nav>

    <div class="name-website">
        <h1 class="webtitle">Can You Solve?</h1>
    </div>
</header>
<main class="main_container">
    <div class="home_article">
        <div id="canvasContainer">
            <canvas id="canvas" width="900" height="520">Your browser does not support Canvase</canvas>
        </div>
        <button id="myButton">Start</button>
        <div class="hangman" data-token="">
            <div class="hangman__question"></div>
            <div class="hangman__word"></div>
            <div class="hangman__guesses"></div>
            <div class="hangman__score"></div>
            <div class="hangman__remaining"></div>
            <form id="hangman-form">
                <label for="guess">Enter a letter:</label>
                <input type="text" id="guess" maxlength="1" autofocus>
            </form>
            <div class="hangman__buttons"></div>

            <button class="hangman__next">Next Question</button>
            <div class="hangman__message"></div>

        </div>
    </div>
    <div class="home_sidebar">


    </div>

</main>
<aside class="sidebar">
</aside>
<footer class="colophon">
    <p>&copy;<?php echo date("Y") ?> Brain Wave Blitz</p>
</footer>
<script type="module" src="can_you_solve.js"></script>
<script>
    function toggleNavMenu() {
        let navLinks = document.getElementById('nav-links');
        if (navLinks.style.height === '0px' || navLinks.style.height === '') {
            navLinks.style.height = 'calc(100vh - 3.125em)';
            navLinks.style.overflowY = 'auto';
        } else {
            navLinks.style.height = '0px';
            navLinks.style.overflowY = 'hidden';
        }
    }
</script>
</body>
</html>
