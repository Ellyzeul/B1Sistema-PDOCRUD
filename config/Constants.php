<?php namespace B1system\Config;


class Constants
{
    public static function load()
    {
        define("HTML_REPLACER", "%_CONTENT_%");
        define("ROOT_DIR", dirname(dirname(__FILE__)));
        define("TEMPLATE_DIR", ROOT_DIR . DIRECTORY_SEPARATOR . "template" . DIRECTORY_SEPARATOR);


        // Estilos de cor das linhas
        define("PHASES_STYLES", [
            "0" => [
                "BACKGROUND_COLOR" => "background-color: #ffffff",
                "COLOR" => "color: #000000",
                "FONT_WEIGHT" => "font-weight: normal"
            ],
            "1.1" => [
                "BACKGROUND_COLOR" => "background-color: #ffffff",
                "COLOR" => "color: #000000",
                "FONT_WEIGHT" => "font-weight: normal"
            ],
            "1.2" => [
                "BACKGROUND_COLOR" => "background-color: #ff66ff",
                "COLOR" => "color: #000000",
                "FONT_WEIGHT" => "font-weight: bold"
            ],
            "1.3" => [
                "BACKGROUND_COLOR" => "background-color: #ff66ff",
                "COLOR" => "color: #000000",
                "FONT_WEIGHT" => "font-weight: bold"
            ],
            "1.4" => [
                "BACKGROUND_COLOR" => "background-color: #ffffff",
                "COLOR" => "color: #000000",
                "FONT_WEIGHT" => "font-weight: normal"
            ],
            "1.5" => [
                "BACKGROUND_COLOR" => "background-color: #ffffff",
                "COLOR" => "color: #000000",
                "FONT_WEIGHT" => "font-weight: normal"
            ],
            "2" => [
                "BACKGROUND_COLOR" => "background-color: #ffe699",
                "COLOR" => "color: #000000",
                "FONT_WEIGHT" => "font-weight: normal"
            ],
            "2.1" => [
                "BACKGROUND_COLOR" => "background-color: #ffff66",
                "COLOR" => "color: #000000",
                "FONT_WEIGHT" => "font-weight: normal"
            ],
            "2.11" => [
                "BACKGROUND_COLOR" => "background-color: #ffff66",
                "COLOR" => "color: #000000",
                "FONT_WEIGHT" => "font-weight: normal"
            ],
            "2.2" => [
                "BACKGROUND_COLOR" => "background-color: #ffe699",
                "COLOR" => "color: #000000",
                "FONT_WEIGHT" => "font-weight: normal"
            ],
            "2.3" => [
                "BACKGROUND_COLOR" => "background-color: #ffff00",
                "COLOR" => "color: #000000",
                "FONT_WEIGHT" => "font-weight: bold"
            ],
            "2.4" => [
                "BACKGROUND_COLOR" => "background-color: #ffd966",
                "COLOR" => "color: #000000",
                "FONT_WEIGHT" => "font-weight: normal"
            ],
            "2.5" => [
                "BACKGROUND_COLOR" => "background-color: #ffd966",
                "COLOR" => "color: #000000",
                "FONT_WEIGHT" => "font-weight: bold"
            ],
            "2.6" => [
                "BACKGROUND_COLOR" => "background-color: #bf8f00",
                "COLOR" => "color: #ffffff",
                "FONT_WEIGHT" => "font-weight: bold"
            ],
            "2.7" => [
                "BACKGROUND_COLOR" => "background-color: #bf8f00",
                "COLOR" => "color: #ffffff",
                "FONT_WEIGHT" => "font-weight: normal"
            ],
            "2.8" => [
                "BACKGROUND_COLOR" => "background-color: #bf8f00",
                "COLOR" => "color: #ffffff",
                "FONT_WEIGHT" => "font-weight: normal"
            ],
            "3.1" => [
                "BACKGROUND_COLOR" => "background-color: #ff3699",
                "COLOR" => "color: #000000",
                "FONT_WEIGHT" => "font-weight: normal"
            ],
            "3.2" => [
                "BACKGROUND_COLOR" => "background-color: #ff3699",
                "COLOR" => "color: #000000",
                "FONT_WEIGHT" => "font-weight: normal"
            ],
            "5.1" => [
                "BACKGROUND_COLOR" => "background-color: #9bc2e6",
                "COLOR" => "color: #000000",
                "FONT_WEIGHT" => "font-weight: normal"
            ],
            "5.2" => [
                "BACKGROUND_COLOR" => "background-color: #9bc2e6",
                "COLOR" => "color: #000000",
                "FONT_WEIGHT" => "font-weight: bold"
            ],
            "5.3" => [
                "BACKGROUND_COLOR" => "background-color: #1f4e78",
                "COLOR" => "color: #ffffff",
                "FONT_WEIGHT" => "font-weight: bold"
            ],
            "5.4" => [
                "BACKGROUND_COLOR" => "background-color: #2f75b5",
                "COLOR" => "color: #ffffff",
                "FONT_WEIGHT" => "font-weight: bold"
            ],
            "5.5" => [
                "BACKGROUND_COLOR" => "background-color: #2f75b5",
                "COLOR" => "color: #ffffff",
                "FONT_WEIGHT" => "font-weight: bold"
            ],
            "6.1" => [
                "BACKGROUND_COLOR" => "background-color: #c6e0b4",
                "COLOR" => "color: #000000",
                "FONT_WEIGHT" => "font-weight: bold"
            ],
            "6.2" => [
                "BACKGROUND_COLOR" => "background-color: #c6e0b4",
                "COLOR" => "color: #000000",
                "FONT_WEIGHT" => "font-weight: bold"
            ],
            "7" => [
                "BACKGROUND_COLOR" => "background-color: #375623",
                "COLOR" => "color: #ffffff",
                "FONT_WEIGHT" => "font-weight: bold"
            ],
            "8.1" => [
                "BACKGROUND_COLOR" => "background-color: #ff5050",
                "COLOR" => "color: #ffffff",
                "FONT_WEIGHT" => "font-weight: bold"
            ],
            "8.12" => [
                "BACKGROUND_COLOR" => "background-color: #ff5050",
                "COLOR" => "color: #000000",
                "FONT_WEIGHT" => "font-weight: bold"
            ],
            "8.2" => [
                "BACKGROUND_COLOR" => "background-color: #ff0000",
                "COLOR" => "color: #ffffff",
                "FONT_WEIGHT" => "font-weight: bold"
            ],
            "8.3" => [
                "BACKGROUND_COLOR" => "background-color: #c00000",
                "COLOR" => "color: #ffffff",
                "FONT_WEIGHT" => "font-weight: bold"
            ],
            "8.4" => [
                "BACKGROUND_COLOR" => "background-color: #c00000",
                "COLOR" => "color: #ffffff",
                "FONT_WEIGHT" => "font-weight: bold"
            ],
            "8.5" => [
                "BACKGROUND_COLOR" => "background-color: #c00000",
                "COLOR" => "color: #ffffff",
                "FONT_WEIGHT" => "font-weight: bold"
            ],
            "8.6" => [
                "BACKGROUND_COLOR" => "background-color: #c00000",
                "COLOR" => "color: #ffffff",
                "FONT_WEIGHT" => "font-weight: bold"
            ]
        ]);
    }
}