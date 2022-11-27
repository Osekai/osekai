<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");

if($_REQUEST['key'] != AUTHENTICATOR_KEY) {
    exit;
}
$discordId = $_POST['discordid'];
echo $discordId;

Database::execOperation("DELETE FROM `AuthenticatorTokens` WHERE `discord_id` = ?;", "i", array($discordId));