<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/osu_api_functions.php");

if(!loggedin()) {
    echo json_encode([]);
    exit;
}

$user = json_decode(v2_getUser($_SESSION['osu']['id'], null, false), true);
$achieved = array();
foreach($user['user_achievements'] as $key => $value) {
    $achieved[$key] = $user['user_achievements'][intval($key)]['achievement_id'];
}
echo json_encode($achieved);
