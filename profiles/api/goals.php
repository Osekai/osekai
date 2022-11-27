<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");

if(isset($_POST['Value']) && !isRestricted()) {
    if(isset($_SESSION['osu']['id'])) {
        Database::execOperation("INSERT IGNORE INTO Goals (UserID, Value, Type, Gamemode, CreationDate) VALUES (?, ?, ?, ?, NOW())", "iiss", array($_SESSION['osu']['id'], $_POST['Value'], $_POST['Type'], $_POST['Gamemode']));
        echo json_encode("Success!");
    }
}

if(isset($_POST['GoalID']) && !isRestricted()) {
    if(isset($_SESSION['osu']['id'])) {
        Database::execOperation("DELETE FROM Goals WHERE ID = ? AND UserID = ?", "ii", array($_POST['GoalID'], $oSession['osu']['id']));
        echo json_encode("Success!");
    }
}

if(isset($_POST['ClaimID']) && !isRestricted()) {
    if(isset($_SESSION['osu']['id'])) {
        Database::execOperation("UPDATE Goals SET Claimed = NOW() WHERE ID = ((? - Value) / 100) AND UserID = ?", "ii", array($_POST['ClaimID'], $oSession['osu']['id']));
        echo json_encode("Success!");
    }
}
?>