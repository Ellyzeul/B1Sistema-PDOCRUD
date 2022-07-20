<?php namespace App\Http\Controllers;

use App\Models\Order;
use \PDOCrud;

class OrderController extends Controller
{
    public static function read(string|null $phase)
    {
        $processed = Order::read($phase);
        $response = [
            "html" => $processed
        ];

        return $response;
    }
}
