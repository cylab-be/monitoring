<?php

namespace App\Sensor;

/**
 * A palette of colors that are used to create graphs.
 *
 * @author tibo
 */
class ColorPalette
{
    const COLORS = ["#003f5c",  "#58508d",  "#8a508f",  "#bc5090",  "#de5a79",  "#ff6361",  "#ff8531", "#ffa600"];


    public static function pick2Colors(int $key) : array
    {
        return [
            self::COLORS[(2 * $key) % count(self::COLORS)],
            self::COLORS[(2 * $key + 1) % count(self::COLORS)]
        ];
    }

    public static function pick1Color(int $key) : string
    {
        return self::COLORS[$key];
    }
}
