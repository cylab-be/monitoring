<?php

namespace App\Http\Controllers;

class ClientController extends Controller
{
    public function get()
    {
        $code = "#!/usr/bin/env php\n\n" .
                file_get_contents(__DIR__ . "/Client/parameters.php") .
                file_get_contents(__DIR__ . "/Client/code.src");
        
        return response($code, 200)
            ->header('Content-Type', 'text/plain');
    }
}
