<?php
ini_set('memory_limit', '256M');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_set_cookie_params([
    'lifetime' => strtotime('+6 months'),
    'path' => '/',
    'domain' => 'localhost',
    'secure' => false, // Since it's not HTTPS, set this to false
    'httponly' => true,
    'samesite' => 'Lax'
]);

session_start();
ob_start(); // turn on output buffering
const DATABASE_HOST = 'localhost';
const DATABASE_NAME = 'general';
const DATABASE_USERNAME = 'root';
const DATABASE_PASSWORD = 'Dpsimfm1964!';