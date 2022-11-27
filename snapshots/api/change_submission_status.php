<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");

if ($_SESSION['role']['rights'] >= 1) {
    $admin_access = true;
} else {
    $admin_access = false;
    exit;
}

$sql = "UPDATE `SnapshotSubmissions` SET `status` = ? WHERE `id` = ?;";

Database::execOperation($sql, "ii", array($_POST['status'], $_POST['id']));

$userid = $_POST['userid'];

Database::execOperation("INSERT INTO Notifications (SystemID, UserID, Message, Title, HTML, Date) VALUES (?, ?, ?, ?, ?, now()) ON DUPLICATE KEY UPDATE UserID = UserID, Message = Message", "sisss", array(NULL, $userid, $_POST['notification'], "Osekai Snapshots Administrators", ""));
// TODO: make and use sendNotification function serverside

echo "SUCCESS";