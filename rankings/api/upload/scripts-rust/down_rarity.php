<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "//global/php/functions.php");

echo json_encode(Database::execSimpleSelect("SELECT * FROM MedalRarity"), JSON_NUMERIC_CHECK);