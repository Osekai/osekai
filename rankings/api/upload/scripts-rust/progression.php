<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "//global/php/functions.php");
include("base_api.php");
$data = json_decode($_POST['data']);

$url = SCRIPTS_WEBHOOK;
$whdata = array('content' => 'SCRIPTS-RUST Progress Update:
' . json_encode($data));
$options = array(
    'http' => array(
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'content' => http_build_query($whdata)
    )
);
$context  = stream_context_create($options);
$result = file_get_contents($url, false, $context); 

$sql = "UPDATE `RankingLoopInfo` SET `CurrentLoop` = ?, `CurrentCount` = ?, `TotalCount` = ?, `EtaSeconds` = ? LIMIT 1;";
$types = "siii";
$data = (array)$data;

$current = $data['current'];
$total = $data['total'];
$eta = $data['eta_seconds'];
$currentLoop = $data['task'];

$xdata = [$currentLoop, $current, $total, $eta];

Database::execOperation($sql, $types, $xdata);