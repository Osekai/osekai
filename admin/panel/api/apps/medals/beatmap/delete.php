<?php
Database::execOperation("DELETE FROM Beatmaps WHERE BeatmapID = ?", "i", array($_REQUEST['strDeletion']));