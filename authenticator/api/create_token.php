<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");

if($_REQUEST['key'] != AUTHENTICATOR_KEY) {
    exit;
}

$discordId = $_GET['discordId'];

$hash = hash("crc32", $discordId, false);

$token = $hash . ":" . bin2hex(random_bytes(4));
echo $token;

$type = "discord";

Database::execOperation("INSERT INTO `AuthenticatorTokens` (`token`, `discord_id`, `type`)
                        VALUES (?, ?, ?);", "sis", array($token, $discordId, $type));