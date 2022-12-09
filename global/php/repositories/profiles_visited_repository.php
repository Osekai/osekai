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

    public static function getRecentlyVisited($userId, $limit = PHP_INT_MAX) {
        return Database::execSelect(
            "SELECT p.osuID as UserID, p.Username as Username FROM ProfilesVisited
            INNER JOIN ProfilesUserinfo p ON p.osuID = visited_id 
            WHERE visited_by = ? GROUP BY UserID ORDER BY MAX(date) DESC LIMIT ?",
            "ii",
            [$userId, $limit]);
    }
}