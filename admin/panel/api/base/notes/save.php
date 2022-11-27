<?php
$userId = $_SESSION['osu']['id'];
$note = $_REQUEST['strNoteContent'];
$noteId = $_REQUEST['strNoteId'];

Database::execOperation("INSERT INTO `AdminNotes` (`Id`, `Author`, `Text`, `Date`)
VALUES (?, ?, ?, now());", "sis", [$noteId, $userId, $note]);

echo json_encode("success");