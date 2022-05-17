<?php namespace B1system\Controller;

use B1system\Model\Order;
use B1system\View\OrderView;
use \PDOCrud;


class OrderController
{
    public static function render() {
        $crud = new PDOCrud();

        $processed = Order::read($crud);
        $response = OrderView::render($processed);

        return $response;
    }
}
