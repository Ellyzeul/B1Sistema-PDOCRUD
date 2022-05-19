<?php
require_once ".." . DIRECTORY_SEPARATOR . "vendor" . DIRECTORY_SEPARATOR . "autoload.php";
require_once "pdocrud" . DIRECTORY_SEPARATOR . "pdocrud.php";

use B1system\Route\Router;
use B1system\Config\Constants;


$dotenv = Dotenv\Dotenv::createImmutable("..");
$dotenv->load();
Constants::load();

$requestURI = explode("?", $_SERVER["REQUEST_URI"]);

$endpoint = $requestURI[0];
$request = [];

if(isset($requestURI[1])) {
    foreach(explode("&", $requestURI[1]) as $param) {
        $pair = explode("=", $param, 2);
        $request = array_merge($request, [$pair[0] => $pair[1] ?? null]);
    }
}

$request = array_merge(
    ["GET" => $request], 
    ["POST" => json_decode(file_get_contents('php://input'), true) ?? []]
);

$response = Router::redirect($endpoint, $request);

echo $response;
