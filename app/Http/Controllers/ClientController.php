<?php

namespace App\Http\Controllers;

class ClientController extends Controller
{
    public function get()
    {
        $code = "#!/usr/bin/env php\n\n" .
                file_get_contents(__DIR__ . "/Client/parameters.php") .
                // remove the <?php
                $this->stripFirstLine(file_get_contents(__DIR__ . "/Client/code.php"));
        
        return response($code, 200)
            ->header('Content-Type', 'text/plain');
    }
    
    public function stripFirstLine($text) : string
    {
        $pos = strpos($text, "\n");
        return $pos === false ? $text : substr($text, $pos + 1);
    }
}
