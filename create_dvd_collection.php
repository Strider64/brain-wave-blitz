<?php
require_once __DIR__ . '/../config/config.php';
require_once "vendor/autoload.php";

use Intervention\Image\ImageManagerStatic as Image;
use brainwave\{
    ErrorHandler,
    Database,
    LoginRepository as Login,
    ImageContentManager
};

$errorHandler = new ErrorHandler();

// Register the exception handler method
set_exception_handler([$errorHandler, 'handleException']);

$database = new Database();
$pdo = $database->createPDO();
$login = new Login($pdo);
if (!$login->check_login_token()) {
    header('location: index.php');
    exit();
}


if (($_SERVER['REQUEST_METHOD'] === 'POST') && isset($_FILES['image'])) {
    $data = $_POST['movies'];

    //echo '<pre>' . print_r($data, 1) . '</pre>';
    //die();
    $data['description'] = trim($data['description']);
    $data['review'] = trim($data['review']);

    $errors = array();
    $exif_data = [];
    $file_name = $_FILES['image']['name']; // Temporary file:
    $file_size = $_FILES['image']['size'];
    $file_tmp = $_FILES['image']['tmp_name'];
    $file_type = $_FILES['image']['type'];
    $file_ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));


    $extensions = array("jpeg", "jpg", "png");

    if (in_array($file_ext, $extensions, true) === false) {
        $errors[] = "extension not allowed, please choose a JPEG or PNG file.";
    }

    if ($file_size >= 58720256) {
        $errors[] = 'File size must be less than or equal to 42 MB';
    }

    /*
     * Create unique name for image.
     */
    $image_random_string = bin2hex(random_bytes(16));
    $image_path = 'assets/dvd_images/img-dvd-' . $image_random_string . '-2048x1365' . '.' . $file_ext;

    move_uploaded_file($file_tmp, $image_path);

    // Load the image
    $image = Image::make($image_path);

    // Resize the image
    $image->resize(2048, 1365, function ($constraint) {
        $constraint->aspectRatio();
        $constraint->upsize();
    });

    // Save the new image
    $image->save($image_path, 100);

    $data['image_path'] = $image_path;

    /*
     * If no errors save ALL the information to the
     * database table.
     */
    if (empty($errors) === true) {

        $movies = new ImageContentManager($pdo, $data, 'movies');
        $result = $movies->create();

        if ($result) {
            header('Location: dashboard.php');
            exit();
        }
    }

}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=yes, initial-scale=1.0">
    <title>Insert DVD Movies</title>
    <link rel="stylesheet" media="all" href="assets/css/admin.css">
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
        <h1 class="webtitle">Create DVD Post</h1>
    </div>

</div>
<div class="main_container">
    <form id="movie_data_entry_form" class="data-form" method="post" enctype="multipart/form-data">
        <input type="hidden" name="action" value="upload">

        <div id="progress_bar_container" class="progress-bar-container">
            <h2 id="progress_bar_title" class="progress-bar-title">Upload Progress</h2>
            <div id="progress_bar" class="progress-bar"></div>
        </div>

        <div id="file_grid_area">
            <input id="file" class="file-input-style" type="file" name="image">
            <label for="file">Select Movie Image</label>
        </div>

        <div id="title_grid_area">
            <label for="title">Movie Title</label>
            <input id="title" type="text" name="movies[title]" required autofocus>
        </div>

        <div id="description_grid_area">
            <label for="description">Description</label>
            <textarea id="description" name="movies[description]"></textarea>
        </div>

        <div id="genre_grid_area">
            <label for="genre">Genre</label>
            <input id="genre" type="text" name="movies[genre]">
        </div>

        <div id="stars_grid_area">
            <label for="stars">Stars</label>
            <input id="stars" type="text" name="movies[stars]">
        </div>

        <div id="director_grid_area">
            <label for="director">Director</label>
            <input id="director" type="text" name="movies[director]">
        </div>

        <div id="rating_grid_area">
            <label for="rating">Rating (1-5)</label>
            <input id="rating" type="number" name="movies[rating]" min="1" max="5">
        </div>

        <div id="review_grid_area">
            <label for="review">Review</label>
            <textarea id="review" name="movies[review]"></textarea>
        </div>

        <div id="submit_movie_grid_area">
            <button type="submit" name="submit" value="enter">Submit</button>
        </div>
    </form>

</div>
<aside class="sidebar">
    <?php $database->showAdminNavigation(); ?>
</aside>
<footer class="colophon">
    <p>&copy; <?php echo date("Y") ?> Add Movies</p>
</footer>
<script src=""></script>
</body>
</html>
