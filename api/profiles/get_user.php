<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");
header("Access-Control-Allow-Origin: *");

// return json
header('Content-Type: application/json');

if (isset($_GET['quick'])) {
    $user = curlRequestUser(htmlspecialchars($_GET['id']));
    echo json_encode($user);
    exit;
}

$cache = Caching::getCache("profiles_" . $_GET['id']);

if ($cache != null) {
    echo $cache;
    exit;
}

$user = v2_getUser(htmlspecialchars($_GET['id']), null, true);
echo $user;

Caching::saveCache("profiles_" . $_GET['id'], "30", $user);

//$id = $_GET['id'];
//$rank = $user['pp_rank'];
//$username = $user['username'];
