<?php
require_once ".." . DIRECTORY_SEPARATOR . "vendor" . DIRECTORY_SEPARATOR . "autoload.php";

use B1system\Route\Router;
use B1system\Dependency\LoadDependency;
use B1system\Config\Constants;


$dotenv = Dotenv\Dotenv::createImmutable("..");
$dotenv->load();
LoadDependency::loadPDOCrud();
Constants::load();

$requestURI = explode("?", $_SERVER["REQUEST_URI"]);

$endpoint = $requestURI[0];
$query_params = [];

if(isset($requestURI[1])) {
    foreach(explode("&", $requestURI[1]) as $param) {
        $pair = explode("=", $param, 2);
        $query_params = array_merge($query_params, [$pair[0] => $pair[1] ?? null]);
    }
}

$response = Router::redirect($endpoint, $query_params);

echo $response;
