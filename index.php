<?php
// Include the configuration file and autoload file from the composer.
require_once __DIR__ . '/../config/config.php';
require_once "vendor/autoload.php";

// Import the ErrorHandler and Database classes from the PhotoTech namespace.
use brainwave\{
    ErrorHandler,
    Database,
    Links,
    ImageContentManager,
    LoginRepository as Login
};



/*
 * Brainwave Blitz 1.0 βeta
 * Created by John Pepp
 * on June 30, 2023
 * Updated by John Pepp
 * on July 8, 2023
 */

// Instantiate the ErrorHandler class
$errorHandler = new ErrorHandler();

// Set the exception handler to use the handleException method from the ErrorHandler instance
set_exception_handler([$errorHandler, 'handleException']);

// Create a new instance of the Database class
$database = new Database();
// Create a PDO instance using the Database class's method
$pdo = $database->createPDO();

$login = new Login($pdo);
if ($login->check_login_token()) {
    header('location: dashboard.php');
    exit();
}

$cms = new ImageContentManager($pdo);

$displayFormat = ["gallery-container w-2 h-2", 'gallery-container w-2 h-2', 'gallery-container w-2 h-2', 'gallery-container h-2', 'gallery-container h-2', 'gallery-container w-2 h-2"', 'gallery-container h-2', 'gallery-container h-2', 'gallery-container w-2 h-2', 'gallery-container h-2', 'gallery-container h-2', 'gallery-container w-2 h-2'];
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['category'])) {
        $category = $_GET['category'];
    } else {
        error_log('Category is not set in the GET data');
        $category = 'general';
    }
    $total_count = $cms->countAllPage($category);
} else {
    try {
        $category = 'general';
        $total_count = $cms->countAllPage($category);
    } catch (Exception $e) {
        error_log('Error while counting all pages: ' . $e->getMessage());
    }
}

/*
 * Using pagination in order to have a nice looking
 * website page.
 */

// Grab the current page the user is on
if (isset($_GET['page']) && !empty($_GET['page'])) {
    $current_page = urldecode($_GET['page']);
} else {
    $current_page = 1;
}

$per_page = 2; // Total number of records to be displayed:


// Grab Total Pages
$total_pages = $cms->total_pages($total_count, $per_page);


/* Grab the offset (page) location from using the offset method */
/* $per_page * ($current_page - 1) */
$offset = $cms->offset($per_page, $current_page);

// Figure out the Links that you want the display to look like
$links = new Links($current_page, $per_page, $total_count, $category);

// Finally grab the records that are actually going to be displayed on the page
$records = $cms->page($per_page, $offset, 'cms', $category);

?>
<!doctype html>
<html lang="en">
<head>
    <!-- Meta tags for responsiveness -->
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=yes, initial-scale=1.0">
    <!-- Title of the web page -->
    <title>Home Page</title>
    <!-- Link to the external CSS file -->
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
        <h1 class="webtitle">Home Page</h1>
    </div>
</header>
<main class="main_container">
    <?php
    foreach ($records as $record) {
        echo '<div class="image-header">';
        echo '<a href="brainwaveblitz.php"><img src="' . $record['image_path'] . '" title="' . $record['heading'] . '" alt="' . $record['heading'] . '"></a>';
        echo '</div>';
        echo '<h1>' . $record['heading'] . '</h1>';
        echo '<p>' . nl2br(htmlspecialchars($record['content'])) . '</p>';
        echo '<br><hr><br>';
    }
    ?>
</main>

<aside class="sidebar">
    <?php echo $links->display_links(); ?>
</aside>

<footer class="colophon">
    <p>&copy; <?php echo date("Y") ?> Brain Wave Blitz</p>
</footer>

</body>
</html>
