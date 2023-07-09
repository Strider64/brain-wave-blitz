<?php
require_once __DIR__ . '/../../config/config.php';
require_once "../vendor/autoload.php";
/*
 * Created on March 23, 2023
 * Updated on March 30, 2023
 * by John R. Pepp
 */


?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=yes, initial-scale=1.0">
    <title>Hangman</title>
    <link rel="stylesheet" media="all" href="../assets/css/hangman.css">
</head>
<body class="site">
<header class="headerStyle">


</header>
<nav class="nav">
    <input type="checkbox" id="nav-check">

    <div class="nav-btn">
        <label for="nav-check">
            <span></span>
            <span></span>
            <span></span>
        </label>
    </div>

    <div class="nav-links">

        <?php

        use FanOfLEGO\Login as LoginAlias;

        if (LoginAlias::adminCheck()) {
            echo '<a href="../admin.php">Admin</a>';
            //echo '<a href="blog.php">Blog</a>';
            echo '<a href="../new_questions.php">New Questions</a>';
            echo '<a href="../modifyTrivia.php">Edit Questions</a>';
            echo '<a href="../createGallery">Create ImageContentManager</a>';
        } elseif (LoginAlias::memberCheck()) {
            echo '<a href="../members.php">Member</a>';
            echo '<a href="../new_questions.php">New Questions</a>';
        } else {
            echo '<a href="../index.php>">Home</a>';
            echo '<a href="../stuff.php">Stuff</a>';
            echo '<a href="../register.php">Register</a>';
            echo '<a href="../contact.php">Contact</a>';
        }
        ?>
        <a href="/hangman/hangman.php">Hangman</a>
        <a href="../trivia.php">Trivia</a>
        <a href="../gallery.php">Gallery</a>

        <?php

        if (isset($_SESSION['id'])) {
            echo '<a href="logout.php">Logout</a>';
            if (!LoginAlias::adminCheck()) {
                echo '<a href="contact.php">Contact</a>';
            }
        } else {
            echo '<a href="login.php">Login</a>';
            if (!LoginAlias::adminCheck()) {
                echo '<a href="contact.php">Contact</a>';
            }
        }
        ?>
    </div>

    <div class="name-website">
        <h1 class="webtitle">Fan of Lego</h1>
    </div>
</nav>
<main id="content" class="main">
    <canvas id="hangman-canvas" width="400" height="400"></canvas>
    <div class="hangman" data-token="<?= $_SESSION['csrf_token'] ?>">
        <div class="hangman__question"></div>
        <div class="hangman__word"></div>
        <div class="hangman__guesses"></div>
        <div class="hangman__remaining"></div>
        <form id="hangman-form">
            <label for="guess">Enter a letter:</label>
            <input type="text" id="guess" maxlength="1" autofocus>
        </form>
        <div class="hangman__buttons"></div>
        <div class="hangman__score"></div>
        <button class="hangman__next">Next Question</button>
        <div class="hangman__message"></div>

    </div>
</main>
<?php include_once "../assets/includes/inc.sidebar.php" ?>
<?php include_once '../assets/includes/inc.footer.php'; ?>
<script src="hangman.js"></script>
</body>
</html>
