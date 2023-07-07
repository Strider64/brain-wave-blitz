<?php
// NavigationMenuTrait.php

namespace brainwave;

use function htmlspecialchars;
use function hash_equals;

trait NavigationMenuTrait
{
    public function regular_navigation(): void
    {
        $navItems = [
            'Home' => 'index.php',
            'Brain Wave Blitz' => 'brainwaveblitz.php',
            'Can You Solve?' => 'canyousolve/can_you_solve.php',
            'Contact' => '#',
        ];

        // Check if the user is logged in
        $isLoggedIn = isset($_COOKIE['login_token']) && isset($_SESSION['login_token']) && hash_equals($_SESSION['login_token'], $_COOKIE['login_token']);

        if ($isLoggedIn) {
            unset($navItems['Home']); // Remove 'Home' from the navigation menu
            $navItems['Dashboard'] = 'dashboard.php'; // Add 'Dashboard' to the navigation menu
        } else {
            $navItems['Login'] = 'login.php'; // Add 'Login' to the navigation menu
        }

        $navLinks = [];

        foreach ($navItems as $title => $path) {
            $href = $this->generateHref($path);
            $safeTitle = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
            $navLinks[] = "<a href=\"{$href}\">{$safeTitle}</a>";
        }

        // Check if the user is logged in
        if ($isLoggedIn) {
            $navLinks[] = '<a href="http://localhost:8888/brainwaveblitz/logout.php">Logout</a>'; // Add 'Logout' to the end of the navigation menu
        }

        echo implode('', $navLinks);
    }

    public function showAdminNavigation(): void
    {
        $adminItems = [
            'Add Game' => '/brainwaveblitz/canyousolve/add_question.php',
            'Edit Game' => '/brainwaveblitz/canyousolve/edit_question.php',
            'New Quest' => '/brainwaveblitz/new_questions.php',
            'Edit Quest' => '/brainwaveblitz/edit_questions.php',
            'Register' => '/brainwaveblitz/register.php',
        ];

        echo '<div class="admin-navigation">';
        foreach ($adminItems as $adminTitle => $adminPath) {
            $adminSafeTitle = htmlspecialchars($adminTitle, ENT_QUOTES, 'UTF-8');
            echo "<a href=\"{$adminPath}\">{$adminSafeTitle}</a>";
        }
        echo '</div>';
    }

    private function generateHref(string $path): string
    {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https://' : 'http://';
        $host = $_SERVER['HTTP_HOST'];

        // Define your base path here
        $base_path = ($host === 'localhost:8888') ? '/brainwaveblitz' : '';

        $base_url = $protocol . $host . $base_path;

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

