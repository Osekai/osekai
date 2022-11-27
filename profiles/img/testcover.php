<?php

$svg = "";

// read from test.svg
$svg = file_get_contents("test.svg");

// svg header when sending
header('Content-Type: image/svg+xml');

// replace PLCHLDR with current date and time - cannot use STR_REPLACE because it is not supported by PHP < 5.3, so we use preg_replace
$content = date("Y-m-d H:i:s");


//$content .= "\n";

//$content .= "Current location: " . $_SERVER['REMOTE_ADDR'];


$svg = preg_replace("/PLCHLDR/", $content, $svg);

echo $svg;