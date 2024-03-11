<?php
Database::execOperation("INSERT INTO `AdminNotes` (`Id`, `Author`, `Text`, `Date`)
VALUES (?, ?, ?, now());", "sis", [$_REQUEST['strPageId'], $_SESSION['osu']['id'], $_REQUEST['strNoteContent']]);