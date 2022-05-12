<?php
$sep = DIRECTORY_SEPARATOR;
require_once ".." . $sep . "vendor" . $sep . "autoload.php";
require_once ".." . $sep . "vendor" . $sep . "pdocrud" . $sep . "pdocrud.php";

use B1system\Route\Router;


$dotenv = Dotenv\Dotenv::createImmutable("..");
$dotenv->load();

$requestURI = explode("?", $_SERVER["REQUEST_URI"]);

$endpoint = $requestURI[0];
$query_params = [];

foreach(explode("&", $requestURI[1]) as $param) {
    $pair = explode("=", $param, 2);
    $query_params = array_merge($query_params, [$pair[0] => $pair[1]]);
}
var_dump($query_params);

Router::redirect($uri, $query_params);
