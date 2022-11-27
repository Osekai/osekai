<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");

$url = "http://".VPS_IP.":23419/template/";
$type = $_GET['type'];
$cache = Caching::getCache("embed_" . $type);
header("Content-Type: image/png");

if ($cache == null) {
    $img = file_get_contents("http://".VPS_IP.":23419/template/" . str_replace(" ", "%20", $type));
    echo $img;
    // 604800 1 week
    Caching::saveCache("embed_" . $type, 604800*3, base64_encode($img));
} else {
    echo base64_decode($cache);
}
