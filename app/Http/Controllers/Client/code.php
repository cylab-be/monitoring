<?php

function usage()
{
    echo "Usage: monitor -i <DEVICE_ID> -t <DEVICE_TOKEN> -s <SERVER_URL>\n";
    exit(1);
}

function parse_env_vars()
{
    global $SERVER, $ID, $TOKEN;
    $SERVER = getenv('SERVER');
    $ID = getenv('ID');
    $TOKEN = getenv('TOKEN');
}

function parse_args()
{
    global $SERVER, $ID, $TOKEN;
    $options = getopt('i:t:s:');


    if (isset($options["i"])) {
        $ID = $options["i"];
    }

    if (isset($options["s"])) {
        $SERVER = $options["s"];
    }
    
    if (isset($options["t"])) {
        $TOKEN = $options["t"];
    }
}

function check_config()
{
    global $SERVER, $ID, $TOKEN;
    if ($SERVER == null || $ID == null || $TOKEN == null) {
        usage();
    }
}

function run_commands()
{
    global $COMMANDS, $ADDITIONAL_COMMANDS, $FUNCTIONS, $RESULTS, $TOKEN, $VERSION;

    foreach ($ADDITIONAL_COMMANDS as $key => $cmd) {
        echo "Running $key ... ";
        $output = shell_exec($cmd . " 2> /dev/null");
        if ($output !== null) {
            echo "ok\n";
            $RESULTS[$key] = trim($output);
        } else {
            echo "E\n";
        }
    }
    
    foreach ($COMMANDS as $key => $cmd) {
        echo "Running $key ... ";
        $output = shell_exec($cmd . " 2> /dev/null");
        if ($output !== null) {
            echo "ok\n";
            $RESULTS[$key] = trim($output);
        } else {
            echo "E\n";
        }
    }

    foreach ($FUNCTIONS as $key => $fn) {
        echo "Running $key ... ";
        $output = @$fn();
        if ($output !== null) {
            echo "ok\n";
            $RESULTS[$key] = trim($output);
        } else {
            echo "E\n";
            $RESULTS[$key] = 'Error executing function';
        }
    }

    // append token and version
    $RESULTS["token"] = $TOKEN;
    $RESULTS["version"] = $VERSION;
}

function upload_results()
{
    global $SERVER, $ID, $RESULTS;
    $URL = $SERVER . "/api/record/" . $ID;
    
    // Encode JSON safely
    $json = json_encode($RESULTS, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

    echo "Upload to $URL ...\n";
    // Upload with cURL
    $ch = curl_init($URL);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // Show result
    echo "Result: $httpCode\n";
}

function run()
{
    global $VERSION;
    echo "Monitoring $VERSION\n";
    echo "https://gitlab.cylab.be/cylab/tokens\n";
    parse_env_vars();
    parse_args();
    check_config();
    run_commands();
    upload_results();
}
