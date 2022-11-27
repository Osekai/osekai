<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "//global/php/functions.php");

$members = [];

foreach(Database::execSimpleSelect("SELECT Id FROM Members") as $member)
{
    $members[] = intval($member['Id']);
}

echo json_encode($members);