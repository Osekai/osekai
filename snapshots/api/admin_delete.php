<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");

if ($_SESSION['role']['rights'] >= 1) {
    $admin_access = true;
} else {
    $admin_access = false;
    exit;
}

$ver =  $_POST['version'];
$ver = str_replace("..", "", $ver);

rmdir("../versions/" . $ver);

echo "SUCCESS";

Caching::wipeCache("snapshots_api");

Database::execSelect("DELETE FROM SnapshotVersions WHERE id = ?", "i", array($_POST['id']));