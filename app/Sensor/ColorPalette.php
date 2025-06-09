<?php

namespace App\Sensor;

/**
 * A palette of colors that are used to create graphs.
 *
 * @author tibo
 */
class ColorPalette
{
    const COLORS = ["#d6e6ff","#d7f9f8","#ffffea","#fff0d4","#fbe0e0","#e5d4ef"];


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

    public static function lighten(string $hex, $ratio) : string
    {
        $hex = preg_replace("/[^0-9a-f]/i", "", $hex);
        $new_hex = '#';
        if (strlen($hex) == 3) {
            $r = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1));
            $g = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1));
            $b = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1));
        } elseif (strlen($hex) == 6) {
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
        } else {
            throw new \Exception("Unrecognized color value: $hex");
        }

        $r = min(255, max(0, $r + $r * $ratio));
        $g = min(255, max(0, $g + $g * $ratio));
        $b = min(255, max(0, $b + $b * $ratio));

        $new_hex .= str_pad(dechex($r), 2, '0', STR_PAD_LEFT);
        $new_hex .= str_pad(dechex($g), 2, '0', STR_PAD_LEFT);
        $new_hex .= str_pad(dechex($b), 2, '0', STR_PAD_LEFT);

        return $new_hex;
    }
}
