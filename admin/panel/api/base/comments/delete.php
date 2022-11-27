<?php
Database::execOperation("DELETE FROM Comments WHERE ID = ?", "i", array($_REQUEST['id']));
?>