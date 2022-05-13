<?php namespace B1system\Route;

class Router
{
    public static function redirect(string $endpoint, array $request)
    {
        $uriParts = explode('/', $endpoint);
        $page = $uriParts[1];

        if(!method_exists(__CLASS__, $page)) return;

        call_user_func(__CLASS__ . "::" . $page, $request);
    }
}