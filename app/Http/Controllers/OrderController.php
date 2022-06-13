<?php namespace App\Http\Controllers;

use App\Models\Order;
use \PDOCrud;


class OrderController
{
    public static function render(string|null $phase)
    {
        $crud = new PDOCrud();

        $processed = Order::read(
            $crud, 
            $phase
        );
        $response = view('orders', [
            'pdocrud' => $processed
        ]);

        return $response;
    }
}
