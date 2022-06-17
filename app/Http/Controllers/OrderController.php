<?php namespace App\Http\Controllers;

use App\Models\Order;
use \PDOCrud;


class OrderController
{
    public static function read(string|null $phase)
    {
        $crud = new PDOCrud();

        $processed = Order::read(
            $crud, 
            $phase
        );
        $response = [
            "html" => $processed
        ];

        return $response;
    }
}
