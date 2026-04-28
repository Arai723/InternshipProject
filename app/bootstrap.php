<?php

session_start();
ini_set('display_errors', '1');
error_reporting(E_ALL);

define('PROJECT_ROOT', dirname(__DIR__));

$appConfig = require PROJECT_ROOT . '/app/config/app.php';
$siteContent = require PROJECT_ROOT . '/app/data/site-content.php';

require_once PROJECT_ROOT . '/app/helpers.php';

$conn = createDatabaseConnection($appConfig['db']);
$currentPage = resolveCurrentPage($_GET['page'] ?? 'internship', $appConfig['allowed_pages']);

