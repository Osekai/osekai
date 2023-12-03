<?php
// for external display
if($_GET['key'] != LANG_UPDATE_KEY) exit;
require_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");
echo json_encode(Database::execSimpleSelect("SELECT * FROM MedalVote"));