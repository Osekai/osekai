<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");

class StatsPageViewsRepository
{
    public static function GetPageViews(SqlTimeSpecifier $since = new SqlTimeSpecifier(SQL_TIMESPECIFIER_UNIT_MONTH, 3), ?string $app = null): array
    {
        $params = [];
        $typeSignature = "";
        $filter = '';

        if (isset($app)) {
            $params[] = $app;
            $typeSignature = "s";
            $filter = " AND app = ? ";
        }

        return Database::execSelect(
            "SELECT COUNT(*) as `views`, COUNT(IF(devicetype = 'desktop', 1, NULL)) as `desktop_views`, 
            COUNT(IF(devicetype = 'mobile', 1, NULL)) as `mobile_views`, DATE(date) as `day` FROM StatsPageViews t 
            WHERE date > DATE_SUB(NOW(), INTERVAL " . $since->getSql() . ")" . $filter . "
            GROUP BY `day` 
            ORDER BY `day` DESC",
            $typeSignature,
            $params
        );
    }

    public static function GetAveragePageLoadtime(SqlTimeSpecifier $since = new SqlTimeSpecifier(SQL_TIMESPECIFIER_UNIT_MONTH, 3), ?string $app = null): array
    {
        $params = [];
        $typeSignature = "";
        $filter = '';

        if (isset($app)) {
            $params[] = $app;
            $typeSignature = "s";
            $filter = " AND app = ? ";
        }

        return Database::execSelect(
            "SELECT AVG(generation_time) as `average_loadtime`,
            COALESCE(AVG(IF(devicetype = 'desktop', generation_time, NULL)), 0) as `average_loadtime_desktop`,
            COALESCE(AVG(IF(devicetype = 'mobile', generation_time, NULL)), 0) as `average_loadtime_mobile`,
            DATE(date) as `day` FROM StatsPageViews t 
            WHERE date > DATE_SUB(NOW(), INTERVAL " . $since->getSql() . ")" . $filter . "
            GROUP BY `day` 
            ORDER BY `day` DESC",
            $typeSignature,
            $params
        );
    }
}