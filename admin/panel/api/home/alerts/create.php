<?php
$name = $_REQUEST['strName'];
$startDate = $_REQUEST['dStartDate'];
$endDate = $_REQUEST['dEndDate'];
$permanent = $_REQUEST['nPermanent'];
$type = $_REQUEST['strType'];
$text = $_REQUEST['strText'];
$apps = $_REQUEST['strApps'];


Database::execOperation("INSERT INTO `Alerts` (`Name`, `StartDate`, `EndDate`, `Permanent`, `Type`, `Text`, `Apps`)
VALUES (?,?,?,?,?,?,?);", "sssisss", [$name, $startDate, $endDate, $permanent, $type, $text, $apps]);