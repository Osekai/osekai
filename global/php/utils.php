<?php

class StatisticsUtils
{
    public static function getGoalStatusFromData($type, $data) {
        if ($type == "PP" && isset($data['statistics']['pp'])) return $data['statistics']['pp'];
        if ($type == "SS Count" && isset($data['statistics']['grade_counts']['ss']) && isset($data['statistics']['grade_counts']['ssh'])) 
            return intval($data['statistics']['grade_counts']['ss']) + intval($data['statistics']['grade_counts']['ssh']);
        if ($type == "Medals" && isset($data['user_achievements'])) return count($data['user_achievements']);
        if ($type == "% Medals" && isset($data['user_achievements_total'])) return $data['user_achievements_total']['completion'];
        if ($type == "Badges" && isset($data['badges'])) return count($data['badges']);
        if ($type == "Rank" && isset($data['statistics']['global_rank'])) return $data['statistics']['global_rank'];
        if ($type == "Country Rank" && isset($data['statistics']['country_rank'])) return $data['statistics']['country_rank'];
        if ($type == "Level" && isset($data['statistics']['level']['current'])) return ['$data']['statistics']['level']['current'];
        if ($type == "Ranked Score" && isset($data['statistics']['ranked_score'])) return $data['statistics']['ranked_score'];

        return 0;
    }

    public static function getScoreForLevel($level) {
        if ($level <= 100) {
            return round(5000 / 3 * (4 * pow($level, 3) - 3 * pow($level, 2) - $level) + 1.25 * pow(1.8, $level - 60));
        } else {
            return 26931190827 + 99999999999 * ($level - 100);
        }
    }

    public static function getLevelProgressFormula($totalScore, $level) : float {
        $scoreLevel = StatisticsUtils::getScoreForLevel($level);
        return min($totalScore / $scoreLevel * 100, 100);
    }

    public static function getProgressDefaultFormula($v1, $v2) : float {
        $value = floatval($v1);

        if ($value == 0)
            return 0;

        return min(floatval($v2) / $value * 100.0, 100.0);
    }

    public static function getGoalProgress($goal)
    {
        $userId = $goal['UserID'];
        $value = $goal['Value'];
        $gamemode = $goal['Gamemode'];
        $type = $goal['Type'];

        $data = json_decode(v2_getUser($userId, $gamemode), null, 512, JSON_OBJECT_AS_ARRAY);

        if ($type == "PP" && isset($data['statistics']['pp'])) 
            return StatisticsUtils::getProgressDefaultFormula($value, StatisticsUtils::getGoalStatusFromData($type, $data));
        
        if ($type == "SS Count" && isset($data['statistics']['grade_counts']['ss']) && isset($data['statistics']['grade_counts']['ssh'])) 
            return StatisticsUtils::getProgressDefaultFormula($value, StatisticsUtils::getGoalStatusFromData($type, $data));
        
        if ($type == "Medals" && isset($data['user_achievements']))            
            return StatisticsUtils::getProgressDefaultFormula($value, StatisticsUtils::getGoalStatusFromData($type, $data));
        
        if ($type == "% Medals" && isset($data['user_achievements_total']))            
            return StatisticsUtils::getProgressDefaultFormula($value, StatisticsUtils::getGoalStatusFromData($type, $data));
        
        if ($type == "Badges" && isset($data['badges']))             
            return StatisticsUtils::getProgressDefaultFormula($value, StatisticsUtils::getGoalStatusFromData($type, $data));
        
        if ($type == "Rank" && isset($data['statistics']['global_rank'])) 
            return StatisticsUtils::getProgressDefaultFormula(StatisticsUtils::getGoalStatusFromData($type, $data), $value);
        
        if ($type == "Country Rank" && isset($data['statistics']['country_rank'])) 
            return StatisticsUtils::getProgressDefaultFormula(StatisticsUtils::getGoalStatusFromData($type, $data), $value);
        
        if ($type == "Level" && isset($data['statistics']['level']['current'])) 
            return StatisticsUtils::getLevelProgressFormula($data['statistics']['level']['current'], $data['statistics']['total_score']);
        
        if ($type == "Ranked Score" && isset($data['statistics']['ranked_score']))
            return StatisticsUtils::getProgressDefaultFormula($value, StatisticsUtils::getGoalStatusFromData($type, $data));

        return 0;
    }
}


class ArrayUtils {
    public static function createArrayWithMissingDays(
        array $array, 
        string $dayKey, 
        int $sinceTime, int $toTime, 
        callable $emptyValueFactory) 
    {
        $newResults = [];

        $date1 = $sinceTime;
        $date2 = $toTime;
        
        $i = count($array) - 1;
        for ($d1 = $date1; $d1<=$date2; $d1 = strtotime("+1 day", $d1)) {
            if ($i >= 0 && strtotime($array[$i][$dayKey]) <= $d1) {
                $newResults[] = $array[$i];
                $i--;
            } else {
                $newResults[] = $emptyValueFactory($d1); 
            }
        }

        return $newResults;
    }
}
