<?php
// for external display
header("Access-Control-Allow-Origin: *");
require_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");
if($_GET['key'] != LANG_UPDATE_KEY) exit;
echo json_encode(Database::execSimpleSelect("SELECT * FROM MedalVote"));