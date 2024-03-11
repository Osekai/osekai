<?php
$user = [];


//include("../../../../../global/php/osu_api_functions.php");
ini_set('display_errors', '0');
ini_set('display_startup_errors', '0');
error_reporting(E_ALL);

header('Content-Type: application/json; charset=utf-8');
$user = json_decode(v2_getUser($_REQUEST['nUserID']), true);
unset($user['page']);
//print_r($user);

$user['versions'] = count(Database::execSelect("SELECT * FROM SnapshotsAzeliaVersions WHERE ArchiverID = ?", "i", [$user['id']]));
$user['beatmaps'] = count(Database::execSelect("SELECT * FROM Beatmaps WHERE SubmittedBy = ?", "i", [$user['id']]));
$user['comments'] = count(Database::execSelect("SELECT * FROM Comments WHERE UserID = ?", "i", [$user['id']]));

echo json_encode($user);