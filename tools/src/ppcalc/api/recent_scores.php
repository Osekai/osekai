<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/osu_api_functions.php");

$modes = ["osu", "taiko", "fruits", "mania"];

$mode = "osu";

if(isset($_GET['mode'])) {
    $mode = $_GET['mode'];
}

if(!in_array($mode, $modes)) {
    echo "mode does not exist";
    exit;
}
if(isset($_SESSION['osu'])) {
    echo v2_recent_scores($mode);
    
} else {
    echo "unauthenticated";
}