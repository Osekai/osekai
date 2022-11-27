<?php
// xhr.send("id=" + submissions[index]['id'] + "&processing=1");

include_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");

if ($_SESSION['role']['rights'] >= 1) {
    $admin_access = true;
} else {
    $admin_access = false;
    exit;
}

$sql = "UPDATE `SnapshotSubmissions` SET `processing` = ? WHERE `id` = ?;";

Caching::wipeCache("snapshots_api");

Database::execOperation($sql, "ii", array($_POST['processing'], $_POST['id']));