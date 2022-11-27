<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");

$badges = Database::execSimpleSelect("SELECT * FROM Badges ORDER BY awarded_at DESC");

echo(json_encode($badges));