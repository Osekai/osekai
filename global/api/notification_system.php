<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");

if(isset($_POST['getNotifs'])) {
    $arrNotifs = Database::execSelect("SELECT * FROM Notifications WHERE UserID = ? AND Cleared = 0", "i", array($_SESSION['osu']['id']));
    echo json_encode($arrNotifs);
}

if(isset($_POST['pushNotif'])) {
    Database::execOperation("INSERT INTO Notifications (SystemID, UserID, Message, Title, HTML, Date) VALUES (?, ?, ?, ?, ?, now()) ON DUPLICATE KEY UPDATE UserID = UserID, Message = Message", "sisss", array((($_POST['sysID'] !== "") ? $_POST['sysID'] : NULL), $_SESSION['osu']['id'], $_POST['pushNotif'], $_POST['notifTitle'], $_POST['notifHTML']));
    echo json_encode("Success!");
}

if(isset($_POST['pushNotifToUser'])) {
    Database::execOperation("INSERT INTO Notifications (SystemID, UserID, Message, Title, HTML, Date) VALUES (?, ?, ?, ?, ?, now()) ON DUPLICATE KEY UPDATE UserID = UserID, Message = Message", "sisss", array((($_POST['sysID'] !== "") ? $_POST['sysID'] : NULL), $_POST['userID'], $_POST['pushNotif'], $_POST['notifTitle'], $_POST['notifHTML']));
    echo json_encode("Success!");
}

if(isset($_POST['markRead'])) {
    Database::execOperation("UPDATE Notifications SET Cleared = 1 WHERE id = ? AND UserID = ?", "ii", array($_POST['id'], $_SESSION['osu']['id']));
    echo json_encode("Success!");
}

if(isset($_POST['ShowCleared'])) {
    $arrNotifs = Database::execSelect("SELECT SystemID, UserID, Title, Message, HTML, Link, Date, Cleared, logo FROM Notifications LEFT JOIN Apps ON Apps.id = Notifications.App WHERE UserID = ? AND Cleared = ?", "ii", array($_SESSION['osu']['id'], (int)$_POST['ShowCleared']));
    echo json_encode($arrNotifs);
}

if(isset($_POST['NotificationRead'])) {
    Database::execOperation("UPDATE Notifications SET Cleared = 1 WHERE id = ? AND UserID = ?", "ii", array($_POST['NotificationRead'], $_SESSION['osu']['id']));
    echo json_encode("Success!");
}
?>