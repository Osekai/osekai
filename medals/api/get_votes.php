<?php
// for external display
require_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");
if($_GET['key'] != LANG_UPDATE_KEY) exit;
echo json_encode(Database::execSimpleSelect("SELECT * FROM MedalVote"));