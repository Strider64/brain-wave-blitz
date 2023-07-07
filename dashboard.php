<?php
require_once 'assets/config/config.php';
require_once "vendor/autoload.php";

// dashhboard.php
/*
 * Brain Wave Blitz
 * Created by John R. Pepp
 * Date Created: July, 12, 2021
 * Last Revision: July 7, 2023 @ 8:00 AM
 * Version: 3.50 ßeta
 *
 */

use brainwave\ErrorHandler;
use brainwave\Database;

use brainwave\ImageContentManager;
use brainwave\Links;
use brainwave\LoginRepository as Login;

$errorHandler = new ErrorHandler();

// Register the exception handler method
set_exception_handler([$errorHandler, 'handleException']);

$database = new Database();
$pdo = $database->createPDO();



$login = new Login($pdo);
// Check to see if user is Logged In
if (!$login->check_login_token()) {
    header('location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=yes, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" media="all" href="assets/css/admin.css">
    <style>

        #myButton {
            outline: none;
            color: #fff;
            border: none;
            background-color: #f12929;
            box-shadow: 2px 2px 1px rgba(0, 0, 0, 0.5);
            width: 6.25em;
            font-family: "Rubik", sans-serif;
            font-size: 1.2em;
            text-transform: capitalize;
            text-decoration: none;
            cursor: pointer;
            padding: 0.313em;
            margin: 0.625em;
            transition: background-color 0.5s;
            float: right;
            text-align: center;
        }

        #myButton:hover {
            background-color: #009578;
        }

        .pagination {
            display: inline-block;
            padding-left: 0;
            margin: 20px 0;
            border-radius: 4px;
        }

        .pagination > li {
            display: inline;
        }

        .pagination > li > a,
        .pagination > li > span {
            position: relative;
            float: left;
            font-size: 1.0em;
            padding: 6px 12px;
            margin-left: -1px;
            line-height: 1.42857143;
            color: #337ab7;
            text-decoration: none;
            background-color: #fff;
            border: 1px solid #ddd;
        }

        .pagination > li:first-child > a,
        .pagination > li:first-child > span {
            margin-left: 0;
            border-top-left-radius: 4px;
            border-bottom-left-radius: 4px;
        }

        .pagination > li:last-child > a,
        .pagination > li:last-child > span {
            border-top-right-radius: 4px;
            border-bottom-right-radius: 4px;
        }

        .pagination > li > a:hover,
        .pagination > li > a:focus {
            color: #23527c;
            background-color: #eee;
            border-color: #ddd;
        }

        .pagination > .active > a,
        .pagination > .active > span,
        .pagination > .active > a:hover,
        .pagination > .active > span:hover,
        .pagination > .active > a:focus,
        .pagination > .active > span:focus {
            z-index: 2;
            color: #fff;
            cursor: default;
            background-color: #337ab7;
            border-color: #337ab7;
        }

        .pagination > li > span {
            display: inline-block;
            padding: 6px 12px;
            color: #999;
            background-color: #fff;
            border: 1px solid #ddd;
            box-sizing: border-box;
            height: 2.313em;
        }

        .pagination > li > span::before {
            content: '...';
            display: inline-block;
            vertical-align: middle;
        }


    </style>


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
        <h1 class="webtitle">Brain Wave Blitz</h1>
    </div>
</header>

<main class="main_container">
    <div class="image-header">
        <img src="assets/images/img-brainwave-header.jpg" alt="Brain Wave Blitz">
    </div>
    <h1>Lorem ipsum dolor sit amet.</h1>
    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Accusantium cupiditate debitis dicta id in laudantium
        maxime numquam sint tenetur veritatis. Aliquam aliquid commodi facilis illo nulla provident quo ratione sed
        sequi veniam? Ab accusamus ad architecto autem distinctio dolore error eum eveniet id illo illum ipsa iure
        labore libero maxime minima nisi nostrum obcaecati officia praesentium quia quibusdam quis quod, soluta
        temporibus vel, vitae! Aliquid blanditiis commodi delectus ea enim ex, harum, laboriosam maiores natus nisi
        optio quam quasi quidem, quisquam recusandae repudiandae saepe sed velit voluptate voluptatem? Atque aut,
        cupiditate deserunt dolor eaque earum enim error excepturi explicabo fugit impedit incidunt ipsum iure nihil
        nulla optio perferendis sit veritatis voluptatem voluptatibus! Alias aut dolore doloribus earum eius est fugit
        hic ipsa iure iusto laborum minus necessitatibus, non obcaecati, odit omnis optio porro quidem reiciendis rerum
        sapiente totam unde velit. Culpa dicta laborum magni nemo quis ut, vitae. Accusamus animi deserunt esse est et
        expedita, explicabo harum maiores modi mollitia nesciunt nisi quaerat, repellat velit voluptatem. Architecto at
        consequatur hic ipsa natus quaerat quidem repudiandae sunt vitae. Consequuntur debitis ea eos et expedita iste
        itaque libero magni neque nobis odio pariatur perspiciatis placeat rerum, unde. Eos nemo, totam. Alias amet,
        aspernatur atque deserunt ea eius eum incidunt ipsam ipsum maiores mollitia nisi nobis nostrum praesentium
        quibusdam, reiciendis repellendus soluta vero. Aut cum dolorem eligendi iste perferendis. Accusamus aliquam
        commodi consequatur consequuntur cum cumque dolor eaque excepturi expedita facilis impedit molestiae natus, odit
        officiis quisquam sint suscipit voluptatibus. Esse impedit magnam magni molestiae veniam. Accusantium dicta
        facere maxime sequi voluptatum? Asperiores autem enim ipsam quasi? At fugit labore nisi tempora. Ab adipisci at
        autem cumque deleniti dignissimos dolor impedit ipsa nihil ullam. Aperiam culpa cumque eius ex exercitationem,
        id incidunt minima mollitia quod rem rerum sit tenetur totam unde voluptate voluptates.</p>
</main>

<aside class="sidebar">
    <div class="admin-navigation">
        <a href="/brainwaveblitz/canyousolve/add_question.php">Add Game</a>
        <a href="/brainwaveblitz/canyousolve/edit_question.php">Edit Game</a>
        <a href="/brainwaveblitz/new_questions.php">New Quest</a>
        <a href="/brainwaveblitz/edit_questions.php">Edit Quest</a>
        <a href="/brainwaveblitz/register.php">Register</a>
    </div>
</aside>
<footer class="colophon">
    <p>&copy; <?php echo date("Y") ?> Brain Wave Blitz</p>
</footer>
</body>
</html>
