<?php
Database::execOperation("DELETE FROM `GroupAssignments` WHERE `UserId`=? AND `GroupId`=?;", "ii", [$_REQUEST['nUserId'], $_REQUEST['nGroupId']]);