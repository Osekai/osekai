<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");
if(!loggedin()){
    echo "Session is invalid. Please log in. 0x000F";
    exit;
}
if (isRestricted()) exit;

$errors = array();

$submission_versionName = htmlspecialchars($_POST['submission_versionName']);
$submission_versionFile = htmlspecialchars($_POST['submission_versionFile']);
$submission_versionInfo = htmlspecialchars($_POST['submission_versionInfo']);
$submission_userID = $_POST['submission_userID'];

if($submission_versionName == ""){
    array_push($errors, "Version Name Empty");
}
if($submission_versionFile == ""){
    array_push($errors, "Version Link Empty");
}

if(count($errors) != 0){
    foreach($errors as $e){
        echo $e . "<br>";
    }
    exit;
}

Database::execOperation("INSERT INTO `SnapshotSubmissions` (`name`, `link`, `info`, `userid`) VALUES (?, ?, ?, ?);", "sssi", array($submission_versionName, $submission_versionFile, $submission_versionInfo, $submission_userID));

echo "SUCCESS";