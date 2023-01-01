<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");

if ($_SESSION['role']['rights'] >= 1) {
    $admin_access = true;
} else {
    $admin_access = false;
    exit;
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ERROR);

$test = Database::execSimpleSelect("SELECT * FROM SnapshotVersions WHERE id = " . htmlspecialchars($_POST['id']));

$thisver = null;

foreach($test as $t){
    if($t['id'] == $_POST['id']){
        $thisver = json_decode($t['json'], true);
    }
}

$ext = pathinfo($_FILES['screenshot']['name'], PATHINFO_EXTENSION);

$filename = "futureupload_" . time() . "." . $ext;

echo $filename;

array_push($thisver['screenshots'], $filename);

$path = "../versions/" . $thisver['version_info']['version'] . "/" . $filename;
echo "<br><br>" . $path;

move_uploaded_file($_FILES["screenshot"]["tmp_name"], $path);

Database::execOperation("UPDATE SnapshotVersions SET json = ? WHERE id = ?", "si", array(json_encode($thisver), $_POST['id']));

Caching::wipeCache("snapshots_api");

redirect("https://osekai.net/snapshots/?version=" . $_POST['id']);