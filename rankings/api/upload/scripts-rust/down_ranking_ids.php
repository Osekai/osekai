<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "//global/php/functions.php");

$members = [];

foreach(Database::execSimpleSelect("SELECT id FROM Ranking") as $member)
{
    $members[] = intval($member['id']);
}

echo json_encode($members, JSON_NUMERIC_CHECK);