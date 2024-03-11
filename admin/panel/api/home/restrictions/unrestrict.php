<?php
$info = Database::execSelect("SELECT * FROM MembersRestrictions WHERE Id = ?", "i", [$_REQUEST['nID']]);
Database::execOperation("DELETE FROM MembersRestrictions WHERE ID = ?", "i", [$_REQUEST['nID']]);
Logging::PutLog("Unrestricted user ".$info['UserID'], -1, 5);