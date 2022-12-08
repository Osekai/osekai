<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");

class GoalsRepository {
    public static function insertGoal(int $userId, string $value, string $type, string $gamemode)
    {
        Database::execOperation(
            "INSERT INTO Goals (UserID, Value, Type, Gamemode, CreationDate) VALUES (?, ?, ?, ?, NOW())", 
            "isss", 
            array($userId, $value, $type, $gamemode)
        );
    }

    public static function deleteGoal(int $goalId, int $userId)
    {
        Database::execOperation(
            "DELETE FROM Goals WHERE ID = ? AND UserID = ?", 
            "ii", 
            array($goalId, $_SESSION['osu']['id'])
        );
    }

    public static function getGoal($userId, $value, $type, $gamemode)
    {
        return Database::execSelectFirstOrNull(
            "SELECT * FROM Goals WHERE UserID = ? AND Value = ? AND Type = ? and Gamemode = ?",
            "isss",
            [$userId, $value, $type, $gamemode]
        );    
    }

    public static function getGoalByClaimId($claimId) {
        return Database::execSelectFirstOrNull(
            "SELECT * FROM Goals WHERE ID = ((? - Value) / 100)",
            "i",
            [$claimId]
        );    
    }

    public static function getGoalById($goalId)
    {
        return Database::execSelectFirstOrNull(
            "SELECT * FROM Goals WHERE ID = ?",
            "i",
            [$goalId]
        );    
    }

    public static function claimGoal($goalId, $userId) {
        Database::execOperation(
            "UPDATE Goals SET Claimed = NOW() WHERE ID = ? AND UserID = ?", 
            "ii", 
            array($goalId, $userId)
        );
    }
}

?>