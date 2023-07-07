<?php
// Load required configuration and libraries
require_once "assets/config/config.php";
require_once "vendor/autoload.php";

/*
 * The Photo Tech Guru
 * Created by John R. Pepp
 * Date Created: July, 12, 2021
 * Last Revision: April 23, 2023
 * Version: 6.02 ßeta
 *
 */

// Import classes
use brainwave\ErrorHandler;
use brainwave\Database;
use brainwave\LoginRepository;

// Create an ErrorHandler instance
$errorHandler = new ErrorHandler();
// Set the exception handler to use the ErrorHandler instance
set_exception_handler([$errorHandler, 'handleException']);

// Create a Database instance and establish a connection
$database = new Database();
$pdo = $database->createPDO();
// Create a LoginRepository instance with the database connection
$login = new LoginRepository($pdo);

// Redirect to dashboard if the user is already logged in
if ($login->check_login_token()) {
    header('Location: dashboard.php');
    exit();
}

// Generate a CSRF token if it doesn't exist and store it in the session
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Process the login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if the submitted CSRF token matches the one stored in the session
    if (hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        // Sanitize the username and password input
        $username = strip_tags($_POST['username']);
        $password = $_POST['password'];

        // Verify the user's credentials
        if ($login->verify_credentials($username, $password)) {
            // Generate a secure login token
            $token = bin2hex(random_bytes(32));
            // Store the login token in the database
            $login->store_token_in_database($_SESSION['user_id'], $token);

            // Set a secure cookie with the login token
            setcookie('login_token', $token, [
                'expires' => strtotime('+6 months'),
                'path' => '/',
                'domain' => DOMAIN,
                'secure' => true,
                'httponly' => true,
                'samesite' => 'Lax'
            ]);

            // Store the login token in the session
            $_SESSION['login_token'] = $token;

            // Redirect the user to the dashboard
            header('Location: dashboard.php');
            exit;
        } else {
            // Display an error message for invalid username or password
            $error = 'Invalid username or password';
            error_log("Login error: " . $error);
        }
    } else {
        // Display an error message
        $error = 'Invalid CSRF token';
        error_log("Login error: " . $error);
        $error = 'An error occurred. Please try again.';
    }
}

// Generate a random nonce value
$nonce = base64_encode(random_bytes(16));

// Set the CSP header
header("Content-Security-Policy: default-src 'self' 'nonce-{$nonce}'; font-src 'self' https://fonts.gstatic.com; style-src 'self' https://fonts.googleapis.com;");
?>


<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=yes, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" media="all" href="assets/css/brainwaveblitz.css">

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
        <h1 class="webtitle">Login Page</h1>
    </div>
</header>

<main class="content">
    <div class="main_container">
        <div class="home_article">
            <form class="login_style" method="post" action="login.php">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <div class="screenName">
                    <label class="text_username" for="username">Username</label>
                    <input id="username" class="io_username" type="text" name="username" value="" required>
                </div>

                <label class="text_password" for="password">Password</label>
                <input id="password" class="io_password" type="password" name="password" required>

                <div class="submitForm">
                    <button class="submitBtn" id="submitForm" type="submit" name="submit" value="login">Login</button>
                </div>
            </form>
        </div>
        <div class="home_sidebar">

        </div>
    </div>
</main>


<aside class="sidebar">

</aside>
<footer class="colophon">
    <p>&copy; <?php echo date("Y") ?> Brain Wave Blitz</p>
</footer>
<script nonce="<?php echo $nonce; ?>">
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
