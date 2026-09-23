<?php

/**
 * Run regexp and return matching group.
 *
 * @param string $pattern
 * @param string $string
 * @param int $match_group
 * @return bool|string
 */
function preg_match_one(string $pattern, string $string, int $match_group = 1)
{
    $matches = [];
    if (preg_match($pattern, $string, $matches) === 1) {
        return $matches[$match_group];
    }

    return false;
}

use Illuminate\Support\Facades\Blade;

function blade(string $template, array $args) : string
{
    return Blade::render(
        file_get_contents($template),
        $args
    );
}
