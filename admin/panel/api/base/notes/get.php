<?php
$noteId = $_REQUEST['strNoteId'];

echo json_encode(Database::execSelect("SELECT * FROM `AdminNotes` WHERE `Id` = ? ORDER BY Date", "s", [$noteId]));