<?php
$config = require __DIR__ . '/env.php';

//hide error messages in production
if ($config['APP_ENV'] === 'production') {
    ini_set('display_errors', 0);
    error_reporting(0);
} else {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
}

$connect = new mysqli(
    $config['DB_HOST'],
    $config['DB_USER'],
    $config['DB_PASS'],
    $config['DB_NAME']
);

if ($connect->connect_error) {
    die("Connection failed: " . $connect->connect_error);
}

?>