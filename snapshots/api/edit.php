<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");

if (checkPermission("apps.snapshots.versions.edit")) {
    $admin_access = true;
} else {
    $admin_access = false;
    exit;
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ERROR);

print_r($_POST);
echo "<br><br>";

$test = Database::execSimpleSelect("SELECT * FROM SnapshotVersions WHERE id = " . htmlspecialchars($_POST['id']));

$thisver = null;

foreach($test as $t){
    if($t['id'] == $_POST['id']){
        $thisver = json_decode($t['json'], true);
    }
}

if($thisver == null){
    echo "that version could not be found. sorry";
    exit;
}

$thisver["version_info"]['release'] = strtotime($_POST['releaseDate']);
$thisver["version_info"]['name'] = $_POST['Name'];
$thisver["version_info"]['version'] = $_POST['Version'];

$thisver["archive_info"]['archiver'] = $_POST['archiverName'];
$thisver["archive_info"]['archiver_id'] = $_POST['archiverID'];
$thisver["archive_info"]['description'] = $_POST['description'];

$autoupdate = false;
if (isset($_POST['autoUpdate']) && $_POST['autoUpdate'] == "on") {
    $autoupdate = true;
} else {
    $autoupdate = false;
}
$thisver["archive_info"]['auto_update'] = $autoupdate;

$requiresserver = false;
if (isset($_POST['requiresServer']) && $_POST['requiresServer'] == "on") {
    $requiresserver = true;
} else {
    $requiresserver = false;
}
$thisver["archive_info"]['requires_supporter'] = $requiresserver;

$group = 1;

if ($_POST['group'] == "stable") {
    $group = 1;
}

if ($_POST['group'] == "lazer") {
    $group = 2;
}

$thisver["archive_info"]['group'] = $group;

if (isset($_POST['video'])) {
    $thisver["archive_info"]['video'] = $_POST['video'];
}

if (isset($_POST['extraInfo'])) {
    $thisver["archive_info"]['extra_info'] = $_POST['extraInfo'];
}

echo json_encode($thisver);

Database::execOperation("UPDATE SnapshotVersions SET json = ? WHERE id = ?", "si", array(json_encode($thisver), $_POST['id']));

redirect("https://osekai.net/snapshots/?version=" . $_POST['id']);