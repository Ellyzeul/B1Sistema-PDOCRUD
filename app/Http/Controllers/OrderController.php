<?php namespace App\Http\Controllers;

use App\Models\Order;
use \PDOCrud;


class OrderController
{
    public static function render()
    {
        $crud = new PDOCrud();

        $processed = Order::read($crud);
        $response = view('orders', [
            'pdocrud' => $processed
        ]);

        return $response;
    }
}
