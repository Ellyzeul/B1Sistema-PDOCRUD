<?php namespace B1system\Dependency;

class LoadDependency
{
    public static function loadPDOCrud()
    {
        $sep = DIRECTORY_SEPARATOR;
        require_once ".." . $sep . "vendor" . $sep . "pdocrud" . $sep . "pdocrud.php";
    }
}