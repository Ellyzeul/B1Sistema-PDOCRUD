<?php namespace B1system\View;

use \PDOCrud;


class OrderView
{
    public static function render($response)
    {
        $template = file_get_contents(TEMPLATE_DIR . "orders.html");
        $content = $response->render();

        $preRendered = \str_replace(HTML_REPLACER, $content, $template);

        return $preRendered;
    }
}
