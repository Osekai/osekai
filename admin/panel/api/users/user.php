<?php
$userid = $_REQUEST['user'];
$user = [];

$user['info'] = json_decode(v2_getUser($userid), true);
$user['comments'] = Database::execSelect("SELECT * FROM Comments WHERE UserID = ?", "i", [$userid]);
$user['beatmaps'] = Database::execSelect("SELECT * FROM Beatmaps WHERE SubmittedBy = ?", "i", [$userid]);
$user['versions'] = Database::execSelect("SELECT * FROM SnapshotsAzeliaVersions WHERE ArchiverID = ?", "i", [$userid]);
$user['submissions'] = Database::execSelect("SELECT * FROM SnapshotSubmissions WHERE userid = ?", "i", [$userid]);

$user['other_info'] = [];
$user['other_info']['last_logon_date'] = Database::execSelect("SELECT sessionLastChange FROM `OsekaiSessions` WHERE `sessionData` LIKE ? ORDER BY `sessionLastChange` DESC LIMIT 1", "s", ['%"id":' . $userid . "%"])[0]['sessionLastChange'];

echo json_encode($user);