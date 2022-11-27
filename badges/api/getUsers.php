<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");

$badges = Database::execSelect("SELECT * FROM Badges WHERE id = ?", "i", array($_GET['badge_id']));
$users = json_decode($badges[0]['users']);

$nu;

for ($i = 0; $i < count($users); $i++)
{
    $nu[$i] = Database::execSelect("SELECT * FROM Ranking WHERE id = ?", "i", array($users[$i]))[0];
}

echo json_encode($nu);