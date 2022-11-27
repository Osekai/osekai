<?php
$tools = [];

function addTool($key, $name, $path) {
    global $tools;
    $tools[] = ["key" => $key, "name" => $name, "path" => $path, "urlpath" => "/tools/".$path];
}

addTool("ppcalc", "PP Calculator", "src/ppcalc");
addTool("medal-percentage-calc", "Medal Percentage Calculator", "src/medal-percentage-calc");