<?php

namespace App\Http\Controllers;

use App\AgentScheduler;

class ClientController extends Controller
{
    public function get(AgentScheduler $scheduler)
    {
        $additional_commands = $scheduler->commands();
        
        
        $code = "#!/usr/bin/env php\n\n" .
                file_get_contents(__DIR__ . "/Client/parameters.php") . "\n" .
                '$ADDITIONAL_COMMANDS = ' . var_export($additional_commands, true) . ";\n\n" .
                // remove the <?php
                $this->stripFirstLine(file_get_contents(__DIR__ . "/Client/code.php")) .
                $this->stripFirstLine(file_get_contents(__DIR__ . "/Client/main.php"));
        
        return response($code, 200)
            ->header('Content-Type', 'text/plain');
    }
    
    public function stripFirstLine($text) : string
    {
        $pos = strpos($text, "\n");
        return $pos === false ? $text : substr($text, $pos + 1);
    }
}
