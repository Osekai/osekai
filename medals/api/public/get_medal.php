<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");

$medalresp = Database::execSelect("CALL FUNC_GetMedals('',?)", "s", array($_GET['medal']));
$beatmaps = Database::execSelect("CALL FUNC_GetBeatmaps(0,?)", "s", array($_GET['medal']));

$medalresp[0]['beatmaps'] = $beatmaps;

echo json_encode($medalresp[0]);