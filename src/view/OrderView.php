<?php namespace B1system\View;

use B1System\Dependency\LoadDependency;


class OrderView
{
    public static function render(\PDOCrud $crud)
    {
        return $crud->render();
    }
}
