<?php
Database::execOperation("DELETE FROM Beatmaps WHERE ID = ?", "i", array($_REQUEST['strDeletion']));
