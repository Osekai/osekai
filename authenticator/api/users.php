<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");

if($_REQUEST['key'] != AUTHENTICATOR_KEY) {
    exit;
}

$users = Database::execSimpleSelect("SELECT * FROM `AuthenticatorUsers`;");

echo json_encode($users);