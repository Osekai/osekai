<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");

if($_REQUEST['key'] != AUTHENTICATOR_KEY) {
    exit;
}

$discord = $_GET['discordId'];
Database::execOperation("DELETE FROM AuthenticatorUsers WHERE DiscordID = ?", "i", [$discord]);
