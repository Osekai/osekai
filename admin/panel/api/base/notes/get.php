<?php
echo json_encode(Database::execSelect("SELECT AdminNotes.*, Ranking.name AS Username
FROM AdminNotes
LEFT JOIN Ranking as Ranking on Ranking.id = AdminNotes.Author 
WHERE AdminNotes.Id = ?", "s", [$_REQUEST['strPageId']]));
?>