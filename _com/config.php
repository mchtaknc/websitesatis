<?php
debug(0);
const BASE_URL = 'https://sitedeposu.com';
date_default_timezone_set('Europe/Istanbul');
$allowedTypes = [
    'image/png' => 'png',
    'image/jpeg' => 'jpg',
];
$uploadErrors = array(
    1 => "The uploaded file exceeds the upload_max_filesize directive in php.ini",
    2 => "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form",
    3 => "The uploaded file was only partially uploaded",
    4 => "No file was uploaded",
    6 => "Missing a temporary folder",
    7 => 'Failed to write file to disk.',
    8 => 'A PHP extension stopped the file upload.',
);
$maxFileSize = 3 * 1024 * 1024;
$maxFileSizeString = substr($maxFileSize, 0, 1);

$dbHost = "localhost";
$dbName = "sitedepo_db";
$dbUser = "sitedepo_usr";
$dbPass = "7eAhduQL6U=W";

try {
    $db = new PDO("mysql:host={$dbHost};dbname={$dbName};charset=utf8", $dbUser, $dbPass);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}