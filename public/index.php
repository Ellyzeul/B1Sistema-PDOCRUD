<?php
$sep = DIRECTORY_SEPARATOR;
require_once ".." . $sep . "vendor" . $sep . "autoload.php";
require_once ".." . $sep . "vendor" . $sep . "pdocrud" . $sep . "pdocrud.php";


$dotenv = Dotenv\Dotenv::createImmutable("..");
$dotenv->load();

$uri = $_SERVER["REQUEST_URI"];
