<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "//global/php/functions.php");
include("base_api.php");

// this needs to fill out MedalListing.php. it's literally duplicate data kind of smashed together so i won't bother bade with it
$url = SCRIPTS_WEBHOOK;
$data = array('content' => 'scripts-rust upload<br>' . json_encode($_POST));
$options = array(
    'http' => array(
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'content' => http_build_query($data)
    )
);
$context  = stream_context_create($options);
$result = file_get_contents($url, false, $context);

$data = json_decode($_POST['data'], true);

Database::execOperation("INSERT INTO `RankingLoopHistory` (`Time`, `LoopType`, `Amount`) VALUES (CURRENT_TIMESTAMP, ?, ?);", "si", [$data['task'], $data['requested_users']]);
Database::execOperation("UPDATE `RankingLoopInfo` SET `CurrentLoop` = ? LIMIT 1;", "s", ["Complete"]);