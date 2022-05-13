<?php namespace B1system\View;

use \PDOCrud;


class OrderView
{
    public static function render(PDOCrud $crud)
    {
        return $crud->render();
    }
}
