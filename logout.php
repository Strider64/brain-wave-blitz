<?php
require_once "assets/config/config.php";
require_once "vendor/autoload.php";


use brainwave\ErrorHandler;
use brainwave\Database;
use brainwave\LoginRepository as Login;

$errorHandler = new ErrorHandler();
$database = new Database();

$pdo = $database->createPDO();

$loginRepository = new Login($pdo);

$loginRepository->logoff();