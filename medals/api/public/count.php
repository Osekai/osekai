<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");

$medals = Database::execSimpleSelect("SELECT * FROM Medals");
// print amount of medals in array
echo count($medals);