<?php
// for external display
if($_GET['key'] != "20dhvkjeh3w9bjhfnh") exit;
require_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");
echo json_encode(Database::execSimpleSelect("SELECT * FROM MedalVote"));