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
 * Brainwave Blitz 2.0 βeta
 * Created by John Pepp
 * on June 30, 2023
 * Updated by John Pepp
 * on September 27, 2023
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
    <meta name="description" content="Brain Wave Blitz offers expertise in web development, design, photography, and online game development. Explore our comprehensive services and innovative solutions.">
    <!-- Title of the web page -->
    <title>Brain Wave Blitz - Web Development, Design, Photography & Game Development</title>

    <link rel="canonical" href="https://www.brainwaveblitz.com/">

    <!-- Link to the external CSS file -->
    <link rel="stylesheet" media="all" href="assets/css/admin.css">
    <script type="application/ld+json">
        {
            "@context": "http://schema.org",
            "@type": "ProfessionalService",
            "additionalType": "http://www.productontology.org/id/Web_developer",
            "name": "Brain Wave Blitz - Web & Game Development, Design, & Photography",
            "address": {
                "@type": "PostalAddress",
                "addressLocality": "Livonia",
                "addressRegion": "Michigan",
                "postalCode": "48150",
                "addressCountry": "United States"
            },
            "telephone": "+1-734-748-7661",
            "email": "jrpepp@pepster.com",
            "url": "https://www.brainwaveblitz.com/",
            "openingHours": "Mo,Tu,We,Th,Fr 09:00-17:00",
            "priceRange": "Depending on the project",
            "description": "Providing top-notch web design and development services for small businesses.",
            "areaServed": "Michigan",
            "founder": {
                "@type": "Person",
                "name": "John Pepp"
            },
            "sameAs": [
                "https://www.facebook.com/Pepster64/",
                "https://www.linkedin.com/in/johnpepp/"
            ]
        }
    </script>

</head>
<body class="site" itemscope itemtype="http://schema.org/WebPage">
<header class="headerStyle" itemprop="header">
    <div class="loginStyle">
        <h1 class="intro" itemprop="headline">Brain Wave Blitz - Web & Game Development, Design, & Photography</h1>
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
    <?php
    foreach ($records as $record) {
        echo '<div class="image-header" itemscope itemtype="http://schema.org/ImageObject">';
        echo '<a href="brainwaveblitz.php" itemprop="url"><img src="' . $record['image_path'] . '" title="' . $record['heading'] . '" alt="' . $record['heading'] . '" itemprop="image"></a>';
        echo '</div>';
        echo '<h2 itemprop="name">' . $record['heading'] . '</h2>';
        echo '<p itemprop="description">'. str_replace('<br />', '<br>', nl2br(htmlspecialchars($record['content']))) . '</p>';
        echo '<br><hr><br>';
    }
    ?>
</main>

<aside class="sidebar">
    <ul class="cards" itemscope itemtype="http://schema.org/ItemList">
        <li class="card-item" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
            <a href="https://flickr.com/photos/pepster/" itemprop="url">
                <figure class="cards" itemscope itemtype="http://schema.org/ImageObject">
                    <img src="assets/images/img_flickr_pictures.jpg" alt="Flickr" width="348" height="174" itemprop="image">
                    <figcaption class="caption">
                        <h3 class="caption-title" itemprop="name">Flickr Images</h3>
                    </figcaption>
                </figure>
            </a>
        </li>
        <li class="card-item" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
            <a href="https://github.com/Strider64/brain-wave-blitz" itemprop="url">
                <figure class="cards" itemscope itemtype="http://schema.org/ImageObject">
                    <img src="assets/images/img_github_repository.jpg" alt="GitHub Repository" itemprop="image">
                    <figcaption class="caption">
                        <h3 class="caption-title" itemprop="name">GitHub Repository</h3>
                    </figcaption>
                </figure>
            </a>
        </li>
        <li class="card-item"  itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
            <a href="https://www.facebook.com/groups/822623719581172/" itemprop="url">
                <figure class="cards" itemscope itemtype="http://schema.org/ImageObject">
                <img src="assets/images/img-facebook-group.jpg" alt="FaceBook Group" itemprop="image">
                    <figcaption class="caption">
                        <h3 class="caption-title" itemprop="name">Facebook Group</h3>
                    </figcaption>
                </figure>
            </a>
        </li>
    </ul>
    <?php echo $links->display_links(); ?>
</aside>

<footer class="colophon"  itemprop="footer">
    <p>&copy; <?php echo date("Y") ?> Brain Wave Blitz - A Livonia, Michigan Developer</p>
</footer>

</body>
</html>
