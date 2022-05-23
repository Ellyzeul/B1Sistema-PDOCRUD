<?php namespace B1system\Route;

use B1system\Route\OrdersRouter;
use B1system\Route\APIRouter;


class Router
{
    public static function redirect(string $endpoint, array $request)
    {
        if($endpoint == "/") {
            \header("Location: /orders");
            die();
        }

        $uriParts = explode('/', $endpoint);
        array_shift($uriParts);
        $routeName = $uriParts[0];

        if(!method_exists(__CLASS__, $routeName)) return;

        return call_user_func(__CLASS__ . "::" . $routeName, $uriParts, $request);
    }

    private static function orders(array $uriParts, array $request)
    {
        return OrdersRouter::call($uriParts, $request);
    }

    private static function api(array $uriParts, array $request)
    {
        return APIRouter::call($uriParts, $request);
    }
}
