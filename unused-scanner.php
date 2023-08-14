<?php
$projectPath = __DIR__ ;

//Declare directories which contains php code
$scanDirectories = [
   $projectPath . '/src/',
];

//Optionally declare standalone files
$scanFiles = [
];


return [
  'composerJsonPath' => $projectPath . '/composer.json',
  'vendorPath' => $projectPath . '/vendor/',
  'scanDirectories' => $scanDirectories,
  'scanFiles' => $scanFiles,
  'skipPackages' => [
    "laravel/framework",
    "guzzlehttp/guzzle", // unused in app/Sensor/ClientVersion, but not detected
    "fideloper/proxy",   // used by laravel
    "laravel/tinker",    // REPL interpreter, required by Laravel (and I don't know why)
    "predis/predis",     // to use Redis for cache and sessions
    "doctrine/dbal"]     // to run some migrations
];

