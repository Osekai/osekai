<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");

if(isset($_POST['Date'])) {
    if (isRestricted()) return;
    if(isset($_SESSION['osu']['id'])) {
        Database::execOperation("INSERT IGNORE INTO Timeline (UserID, Date, Note, Mode) VALUES (?, ?, ?, ?)", "isss", array($_SESSION['osu']['id'], $_POST['Date'], $_POST['Note'], $_POST['Mode']));
        echo json_encode("Success!");
    }
}

if(isset($_POST['NewDate'])) {
    if (isRestricted()) return;
    if(isset($_SESSION['osu']['id'])) {
        if(strtotime($_POST['NewDate']) > strtotime("now")) {
            echo json_encode("Date cannot be in future.");
            exit;
        }
        Database::execOperation("UPDATE Timeline SET Note = ?, Date = ? WHERE UserID = ? AND id = ?", "ssii", array($_POST['NewNote'], $_POST['NewDate'], $_SESSION['osu']['id'], $_POST['ItemId']));
        echo json_encode("Success!");
    }
}

if(isset($_POST['Remove'])) {
    if (isRestricted()) return;
    if(isset($_SESSION['osu']['id'])) {
        Database::execOperation("DELETE FROM Timeline WHERE UserID = ? AND id = ?", "ii", array($_SESSION['osu']['id'], $_POST['Remove']));
        echo json_encode("Success!");
    }
}
?>