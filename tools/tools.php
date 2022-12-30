<?php
$tools = [];

function addTool($key, $name, $path) {
    global $tools;
    $tools[] = ["key" => $key, "name" => $name, "path" => $path, "urlpath" => "/tools/".$path];
}

addTool("ppcalc", "PP Calculator", "src/ppcalc");
addTool("medal-percentage-calc", "Medal Percentage Calculator", "src/medal_percentage_calc");
addTool("comparer", "Comparer", "src/comparer");
addTool("medal-name-quiz", "Medal Name Quiz", "src/medal_name_quiz");
addTool("stdev-pp-calc", "Standard Deviated PP Calculator", "src/stdev_pp_calc");