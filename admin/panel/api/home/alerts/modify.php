<?php
$name = $_REQUEST['strName'];
$startDate = $_REQUEST['dStartDate'];
$endDate = $_REQUEST['dEndDate'];
$permanent = $_REQUEST['nPermanent'];
$type = $_REQUEST['strType'];
$text = $_REQUEST['strText'];
$apps = $_REQUEST['strApps'];

$alertId = $_REQUEST['nAlertId'];

Database::execOperation("UPDATE `Alerts` SET
`Name` = ?,
`StartDate` = ?,
`EndDate` = ?,
`Permanent` = ?,
`Type` = ?,
`Text` = ?,
`Apps` = ?
WHERE `Id` = '5';", "sssisssi", [$name, $startDate, $endDate, $permanent, $type, $text, $apps, $alertId]);