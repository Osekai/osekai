<?php
Database::execOperation("INSERT INTO `GroupAssignments` (`UserId`, `GroupId`) VALUES (?, ?);", "ii", [$_REQUEST['nUserId'], $_REQUEST['nGroupId']]);