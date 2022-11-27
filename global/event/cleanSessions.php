<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");

// remove all from OsekaiSessions where sessionData does not include locale or token

$sql = "DELETE FROM OsekaiSessions WHERE sessionData NOT LIKE ? OR sessionData NOT LIKE ?";
//Database::execOperation($sql, "ss", ["%loacle%", "%token%"]);