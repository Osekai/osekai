<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");

$medals = Database::execSimpleSelect("SELECT * FROM Medals");

header('Content-Type: application/json; charset=utf-8');
echo json_encode($medals);
