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
error_reporting(E_ALL);

print_r($_POST);
echo "<br><br>";

$test = Database::execSimpleSelect("SELECT * FROM SnapshotVersions WHERE id = " . htmlspecialchars($_POST['id']));

$thisver = null;

foreach($test as $t){
    if($t['id'] == $_POST['id']){
        $thisver = json_decode($t['json'], true);
    }
}

unlink("../versions/" . $thisver['version_info']['version'] . "/" . $thisver['screenshots'][$_POST['screenshotIndex']]);

unset($thisver['screenshots'][$_POST['screenshotIndex']]); // remove item at index 0
$thisver['screenshots'] = array_values($thisver['screenshots']); // 'reindex' array

echo json_encode($thisver);

Caching::wipeCache("snapshots_api");

Database::execOperation("UPDATE SnapshotVersions SET json = ? WHERE id = ?", "si", array(json_encode($thisver), $_POST['id']));