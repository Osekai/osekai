<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "//global/php/functions.php");
include("base_api.php");
$data = json_decode($_POST['data'], true);

// this needs to fill out MedalListing.php. it's literally duplicate data kind of smashed together so i won't bother bade with it
$url = SCRIPTS_WEBHOOK;
$data = array('content' => 'SCRIPTS-RUST Progress Update:
' . json_encode($data));
$options = array(
    'http' => array(
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'content' => http_build_query($data)
    )
);
$context  = stream_context_create($options);
$result = file_get_contents($url, false, $context); 

$sql = "UPDATE `RankingLoopInfo` SET `CurrentLoop` = ?, `CurrentCount` = ?, `TotalCount` = ?, `EtaSeconds` = ? LIMIT 1;";

$types = "siii";

$currentLoop = "Unknown";
//if(isset($_POST['data']['loop'])) {
//    $currentLoop = $_POST['data']['loop'];
//}


$current = $data['current'];
$total = $data['total'];
$eta = $data['eta_seconds'];

$data = [$currentLoop, $current, $total, $eta];



Database::execOperation($sql, $types, $data);