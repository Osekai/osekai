<?php

if(count(Database::execSelect("SELECT * FROM MembersRestrictions WHERE Active = 1 AND UserID = ?", "i", [$_REQUEST['nUserID']])) == 0) {
Database::execOperation("INSERT INTO `MembersRestrictions` (`UserID`, `Time`, `Reason`, `Active`)
VALUES (?, NOW(), ?, 1);", "is", [$_REQUEST['nUserID'], $_REQUEST['strReason']]);
Logging::PutLog("Restricted user ".$_REQUEST['nUserID']." with reason ".$_REQUEST['strReason'], -1, 5);
} else {
    echo json_encode("User already has an active restriction");
    exit;
}