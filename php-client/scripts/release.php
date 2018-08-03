#!/usr/bin/env php
<?php
$date = date("Ymd.His");

file_put_contents(__DIR__ . "/../version", $date);
exec(__DIR__ . "/../vendor/bin/box build");
file_put_contents(__DIR__ . "/../version", "@dev");

$source = __DIR__ . "/../bin/monitor.phar";
$sha1 = sha1(file_get_contents($source));
$target = __DIR__ . "/../release/monitor-$date.phar";

$manifest = file_get_contents("manifest.json.tmpl");
$manifest = str_replace("{{version}}", $date, $manifest);
$manifest = str_replace("{{sha1}}", $sha1, $manifest);

file_put_contents(__DIR__ . "/../release/manifest.json", $manifest);
copy($source, $target);
