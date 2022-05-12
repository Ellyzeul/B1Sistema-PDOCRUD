<?php

class Router
{
    public static function redirect(string $uri, array $request)
    {
        $uriParts = explode('/', $uri);
        $model = $uriParts[1];
        $operation = $uriParts[2];

        if(!method_exists(__CLASS__, $model)) return;

        call_user_func(__CLASS__ . "::" . $model, $operation, $request);
    }
}