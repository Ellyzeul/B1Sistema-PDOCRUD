<?php namespace B1system\Controller;

use B1system\View\OrderView;
use \PDOCrud;


class OrderController
{
    public static function render() {
        $crud = new PDOCrud();

        $response = OrderView::render($crud);

        return $response;
    }

}
