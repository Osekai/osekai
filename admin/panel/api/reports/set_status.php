<?php
Database::execOperation("UPDATE `Reports` SET `Status` = ? WHERE `Id` = ?;", "ii", [$_REQUEST['nStatus'], $_REQUEST['nReportId']]);