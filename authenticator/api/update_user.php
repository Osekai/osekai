<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");

if($_REQUEST['key'] != AUTHENTICATOR_KEY) {
    exit;
}

$discordId = $_GET['discordId']; // this is just to find the user
$medals = $_GET['medalCount'];
$percentage = $_GET['percentage'];

echo $percentage;

Database::execOperation("UPDATE `AuthenticatorUsers` SET `MedalCount` = ?, `Percentage` = ? WHERE `DiscordID` = ?;", "isi", array($medals, $percentage, $discordId));