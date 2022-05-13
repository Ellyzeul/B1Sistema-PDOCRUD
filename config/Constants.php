<?php namespace B1system\Config;


class Constants
{
    public static function load()
    {
        define("HTML_REPLACER", "%_CONTENT_%");
        define("ROOT_DIR", dirname(dirname(__FILE__)));
        define("TEMPLATE_DIR", ROOT_DIR . DIRECTORY_SEPARATOR . "template" . DIRECTORY_SEPARATOR);
    }
}