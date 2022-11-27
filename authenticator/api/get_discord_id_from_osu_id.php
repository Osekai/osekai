<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");

if($_REQUEST['key'] != AUTHENTICATOR_KEY) {
    exit;
}

$osu = $_GET['osu'];
echo Database::execSelect("SELECT * FROM AuthenticatorUsers WHERE osuID = ?", "i", [$osu])[0]['DiscordID'];