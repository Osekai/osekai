<?php
echo json_encode(Database::execSelect("SELECT Comments.*, Ranking.name AS Username
FROM Comments
LEFT JOIN Ranking as Ranking on Ranking.id = Comments.UserID 
WHERE Comments.Id = ?", "i", [$_REQUEST['nCommentId']]));
?>