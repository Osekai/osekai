<?php
$alertId = $_REQUEST['nAlertId'];

Database::execOperation("DELETE FROM `Alerts`
WHERE ((`Id` = ?));", "i", $alertId);