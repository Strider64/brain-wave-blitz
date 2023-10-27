<?php
// Include the configuration file and autoload file from the composer.
require_once __DIR__ . '/../config/config.php';
require_once "vendor/autoload.php";

/*
 * Gallery
 * Created by John R. Pepp
 * Date Created: July, 12, 2021
 * Last Revision: September 23, 2023
 * Version: 4.00 ßeta
 *
 */

use brainwave\ErrorHandler;
use brainwave\Database;
use brainwave\LoginRepository as Login;
use brainwave\ImageContentManager;

$errorHandler = new ErrorHandler();

// Register the exception handler method
set_exception_handler([$errorHandler, 'handleException']);

$database = new Database();
$pdo = $database->createPDO();

$count = new ImageContentManager($pdo);

$total = $count->countAllGallery('wildlife');
// New Instance of Login Class
$login = new Login($pdo);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=yes, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Photo Gallery</title>
    <style>
        .sidebar_pages {
            display: flex;
            -webkit-flex-wrap: wrap;
            flex-wrap: wrap;
            justify-content: flex-start;
            height: auto;
            width: 100%;
        }
    </style>
    <link rel="stylesheet" media="all" href="assets/css/gallery.css">
    <script type="application/ld+json">
        {
            "@context": "http://schema.org",
            "@type": "WebPage",
            "name": "Photo Gallery",
            "description": "A collection of various photos from different categories.",
            "url": "https://www.brainwaveblitz.com/gallery.php",
            "about": {
                "@type": "ImageObject",
                "contentUrl": "https://www.brainwaveblitz.com/assets/puzzle_images/img-7e4d33f5b396ec6d9fae4ba09a95197c-600x400.jpeg",
                "description": "A sample image from the gallery",
                "name": "Gallery Image"
            },
            "publisher": {
                "@type": "Organization",
                "name": "Brain Wave Blitz Image Gallery",
                "url": "https://www.brainwaveblitz.com"
            }
        }
    </script>


</head>
<body class="site" itemscope itemtype="http://schema.org/WebPage">
<header class="headerStyle" itemprop="header">
    <div class="loginStyle">
        <h1 class="intro" itemprop="headline">Brain Wave Blitz - Image Gallery</h1>
    </div>
</header>
<div class="nav">
    <!-- Button for mobile navigation -->
    <button class="nav-btn" id="nav-btn">
        <span></span>
        <span></span>
        <span></span>
    </button>

    <!-- Navigation links -->
    <nav class="nav-links" id="nav-links" itemprop="breadcrumb">
        <?php $database->regular_navigation(); ?>
    </nav>
</div>

<main class="main_container">
    <div class="container">

    </div>
</main>


<aside class="sidebar">
    <?php
    if ($login->check_login_token()) {
        $database->showAdminNavigation();
    }
    ?>
    <form id="gallery_category" action="gallery.php" method="post">
        <label for="category">Category:</label>
        <select id="category" class="select-css" name="category" tabindex="1">

            <option value="general">General</option>
            <option selected value="wildlife">Wildlife</option>
            <option value="landscape">Landscape</option>
            <option value="lego">LEGO</option>
            <option value="halloween">Halloween</option>
        </select>
    </form>
    <div class="sidebar_pages">

    </div>
</aside>
<div class="lightbox">

</div>
<footer class="colophon">
    <p>&copy; <?php echo date("Y") ?> Photo Gallery</p>
</footer>

<script src="assets/js/images.js"></script>
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
