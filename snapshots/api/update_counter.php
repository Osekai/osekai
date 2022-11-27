<?php
if(!$_POST['verID']){
    echo "Ver ID not posted.";
    exit;
}

include_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");

if($_POST['type'] == "download"){
    Database::execOperation("UPDATE SnapshotVersions SET downloads = downloads + 1 WHERE id = ?", "i", array($_POST['verID']));
}else if($_POST['type'] == "view"){
    Database::execOperation("UPDATE SnapshotVersions SET views = views + 1 WHERE id = ?", "i", array($_POST['verID']));
}else{
    echo "Could not get type";
    exit;
}