<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");

if (checkPermission("apps.snapshots.versions.edit") || $_POST['bykey'] == "f09jf03jf93290fj2") {
    $admin_access = true;
} else {
    $admin_access = false;
    exit;
}

$version = json_decode(Database::execSelect("SELECT * FROM SnapshotVersions WHERE id = ?", "i", array($_POST['id']))[0]['json'], true);
$index = 0;

$downloads = $version['downloads'];

$newDownload = [];
$newDownload['name'] = $_POST['name'];
$newDownload['link'] = $_POST['link'];

array_push($downloads, $newDownload);

$version['downloads'] = $downloads;

echo json_encode($version);

Database::execOperation("UPDATE SnapshotVersions SET json = ? WHERE id = ?", "si", array(json_encode($version), $_POST['id']));

Caching::wipeCache("snapshots_api");

redirect("https://osekai.net/snapshots/?version=" . $_POST['id']);