<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");


$availableAlerts = Database::execSimpleSelect("SELECT * FROM `Alerts` WHERE `StartDate` < NOW() AND `EndDate` > NOW()");

$finalAlerts = [];

for($x = 0; $x < count($availableAlerts); $x++) {
    if(count(json_decode($availableAlerts[$x]['Apps'])) == 0) {
        $finalAlerts[] = $availableAlerts[$x];
        continue;
    }
    if(in_array($_GET['app'], json_decode($availableAlerts[$x]['Apps']))) {
        $finalAlerts[] = $availableAlerts[$x];
    }
}

echo json_encode($finalAlerts);