<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");

$versions = Database::execSimpleSelect("SELECT * FROM SnapshotsAzeliaVersions");

for($i = 0; $i < count($versions); $i++) {
    $downloads = Database::execSelect("SELECT * FROM SnapshotsAzeliaDownloads WHERE ReferencedVersion = ?", "i", array($versions[$i]["Id"]));
    $versions[$i]["VersionDownloads"] = $downloads;

    $screenshots = Database::execSelect("SELECT * FROM SnapshotsAzeliaScreenshots WHERE ReferencedVersion = ?", "i", array($versions[$i]["Id"]));
    $versions[$i]["VersionScreenshots"] = $screenshots;
}

header('Content-Type: application/json');
echo(json_encode($versions));