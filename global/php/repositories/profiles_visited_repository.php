<?php

class ProfilesVisitedRepository {
    public static function getMostVisited($limit = PHP_INT_MAX) {
        return Database::execSelect(
            "SELECT p.osuID as UserID, p.Username as Username, COUNT(*) AS visits FROM ProfilesVisited 
            INNER JOIN ProfilesUserinfo p ON p.osuID = visited_id GROUP BY visited_id 
            ORDER BY visits DESC LIMIT ?",
            "i",
            [$limit]);
    }
}