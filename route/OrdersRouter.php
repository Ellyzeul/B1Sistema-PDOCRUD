<?php namespace B1system\Route;

use B1system\Controller\OrderController;


class OrdersRouter
{
    public static function call(array $uriParts, array $request)
    {
        return OrderController::render();
    }
}