<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");

if($_REQUEST['key'] != AUTHENTICATOR_KEY) {
    exit;
}

print_r($_POST);

$discordID = $_POST['discordid'];
$osuID = $_POST['osuid'];
$osuUsername = $_POST['username'];
$medalCount = $_POST['medalcount'];#
$percentage = $_POST['percentage'];
// print errors
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

Database::execOperation("INSERT INTO `AuthenticatorUsers` (`osuID`, `DiscordID`, `osuUsername`, `MedalCount`, `Percentage`, `Type`)
                        VALUES (?, ?, ?, ?, ?, 'discord');", "issis", array($osuID, $discordID, $osuUsername, $medalCount, $percentage));