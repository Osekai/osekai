<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");

$version = json_decode(Database::execSelect("SELECT * FROM SnapshotVersions WHERE id = ?", "i", array($_POST['id']))[0]['json'], true);
$index = 0;

$downloads = [];

foreach($version['downloads'] as $download){
    echo $download['name'] . ":" . $_POST['downloadName'] . "\n\n";
    if($download['name'] == $_POST['downloadName']){
        echo "found it";
    }else{
        array_push($downloads, $download);
    }
    $index += 1;
}

$version['downloads'] = $downloads;

echo json_encode($version);

Caching::wipeCache("snapshots_api");

Database::execOperation("UPDATE SnapshotVersions SET json = ? WHERE id = ?", "si", array(json_encode($version), $_POST['id']));