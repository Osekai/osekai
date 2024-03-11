<?php
echo json_encode(Database::execSelect("SELECT Beatmap.*, Ranking.name as SubmittedUsername FROM Beatmaps AS Beatmap LEFT JOIN Ranking as Ranking on Ranking.id = Beatmap.SubmittedBy WHERE Beatmap.BeatmapID = ? LIMIT 1;", "i", [$_REQUEST["numBeatmapSet"]]));
