<?php
$sep = DIRECTORY_SEPARATOR;
require_once ".." . $sep . "vendor" . $sep . "autoload.php";

use B1system\Route\Router;
use B1system\Dependency\LoadDependency;


$dotenv = Dotenv\Dotenv::createImmutable("..");
$dotenv->load();
LoadDependency::loadPDOCrud();

$requestURI = explode("?", $_SERVER["REQUEST_URI"]);

$endpoint = $requestURI[0];
$query_params = [];

if(isset($requestURI[1])) {
    foreach(explode("&", $requestURI[1]) as $param) {
        $pair = explode("=", $param, 2);
        $query_params = array_merge($query_params, [$pair[0] => $pair[1] ?? null]);
    }
}

echo Router::redirect($endpoint, $query_params);
