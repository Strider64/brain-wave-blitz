<?php
// NavigationMenuTrait.php

namespace brainwave;

use htmlspecialchars;

trait NavigationMenuTrait
{
    public function regular_navigation(): void
    {
        $navItems = [
            'Home' => 'index.php',
            'Brainwave Blitz' => 'brainwaveblitz.php',
            'Can You See?' => '#',
            'Contact' => '#',
        ];

        foreach ($navItems as $title => $path) {
            $href = $this->generateHref($path);
            $safeTitle = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
            echo "<a href=\"{$href}\">{$safeTitle}</a>";
        }

        // Check for the presence of the cookie
        if (isset($_COOKIE['login_token'])) {
            // Verify the token against the stored value
            $stored_token = $_SESSION['login_token'] ?? '';

            // User is Not logged In
            if (!hash_equals($stored_token, $_COOKIE['login_token'])) {
                $loginHref = $this->generateHref('admin/login.php');
                echo "<a href=\"{$loginHref}\">Login</a>";
            }
        }

        // Add this line for testing purposes
        //echo "<a href=\"https://www.phototechguru.com/admin/login.php\">Test Login</a>";
    }

    private function generateLoginHref(string $current_dir): string
    {
        if ($current_dir == '/admin') {
            $path = 'login.php';
        } else {
            $path = 'admin/login.php';
        }

        return $path;
    }

    public function showAdminNavigation(): void
    {
        $navItems = [
            'Dashboard' => '../dashboard.php',
            'Add Game' => '/hangman/add_question.php',
            'Edit Game' => '/hangman/edit_question.php',
            'New Quest' => '/new_questions.php',
            'Edit Quest' => '/edit_questions.php',
            'Register' => '/register.php',
            'Logout' => '/admin/logout.php',
        ];

        echo '<div class="admin-navigation">';
        foreach ($navItems as $title => $path) {
            $safeTitle = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
            echo "<a href=\"{$path}\">{$safeTitle}</a>";
        }
        echo '</div>';
    }

    private function generateHref(string $path): string
    {
        $protocol = ($_SERVER['HTTPS'] && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
        $host = $_SERVER['HTTP_HOST'];

        // Define your base path here
        $base_path = ($host === 'localhost:8888') ? 'localhost:8888/brainwaveblitz' : $host;

        $base_url = $protocol . $base_path;

        // Build the URL first, then validate it
        $url = $base_url . '/' . $path;
        $sanitized_url = filter_var($url, FILTER_SANITIZE_URL);
        $valid_url = filter_var($sanitized_url, FILTER_VALIDATE_URL);

        if ($valid_url === false) {
            die('Invalid URL');
        }

        return $valid_url;
    }





}
