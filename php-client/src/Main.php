#!/usr/bin/env php

<?php
/**
 * Command line entry point.
 */
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/autoload.php';

use Symfony\Component\Console\Application;
$version = date(file_get_contents(__DIR__ . "/../version"));

$application = new Application("PHP Monitor Client", $version);
$application->add(new Monitor\PingCommand());
$application->run();
