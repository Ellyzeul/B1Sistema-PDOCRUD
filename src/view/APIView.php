<?php namespace B1system\View;


class APIView
{
    public static function render(array $response)
    {
        header("Content-Type: application/json");

        $preRendered = json_encode($response);

        return $preRendered;
    }
}
