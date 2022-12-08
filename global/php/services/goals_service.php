<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");

utils_classes();
goals_repository();

define("VALUE_DECIMAL_REGEX", "/^\d+(?:\.\d{1,2})?$/");
define("VALUE_INT_REGEX", "/^\d+$/");
define("VALID_GAMEMODES", array("taiko", "mania", "osu", "fruits", "all"));
define("TYPES_WITH_VALUE_DECIMAL", array("PP", "% Medals", "Level", "Ranked Score"));
define("VALID_TYPES", array("PP", "Rank", "Country Rank", "Medals", "% Medals", "SS Count", "Badges", "Level", "Ranked Score"));

class GoalsService {
    public static function addGoal($userId, $type, $gamemode, $value)
    {
        if (!in_array($type, VALID_TYPES))
            throw new InvalidParameterException("Invalid Type");

        if (!in_array($gamemode, VALID_GAMEMODES))
            throw new InvalidParameterException("Invalid Gamemode");

        $can_have_decimal_value = in_array($type, TYPES_WITH_VALUE_DECIMAL);

        if (
            ($can_have_decimal_value && !preg_match(VALUE_DECIMAL_REGEX, $value)) || 
            (!$can_have_decimal_value && !preg_match(VALUE_INT_REGEX, $value))
        ) {
            throw new InvalidParameterException("Invalid Value format");
        }

        if (GoalsRepository::getGoal($userId, $value, $type, $gamemode) != null)
            throw new InvalidParameterException("Goal already exists");

        GoalsRepository::insertGoal($userId, $value, $type, $gamemode);
    }

    public static function removeGoal($goalId, $userId)
    {
        if (!filter_var($goalId, FILTER_VALIDATE_INT))
            throw new InvalidParameterException("Invalid GoalID");

        GoalsRepository::deleteGoal($goalId, $userId);
    }

    public static function claimGoal($claimId, $userId)
    {
        if (!filter_var($claimId, FILTER_VALIDATE_INT))
            throw new InvalidParameterException("Invalid GoalID");

        if (!filter_var($userId, FILTER_VALIDATE_INT))
            throw new InvalidParameterException("Invalid UserID");

        $goal = GoalsRepository::getGoalByClaimId($claimId);

        if ($goal == null)
            throw new ResourceNotFoundException("The goal was not found");

        $progress = StatisticsUtils::getGoalProgress($goal);

        if ($progress < 100)
            throw new InvalidOperationException("Goal progress is not greater or equal than 100, it cannot be claimed");

        GoalsRepository::claimGoal($goal['ID'], $userId);
    }
}

?>