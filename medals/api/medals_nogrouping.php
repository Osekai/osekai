<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");

$medals = Database::execSimpleSelect("SELECT * FROM Medals");

echo json_encode($medals);