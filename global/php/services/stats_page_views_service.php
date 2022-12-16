<?php

require_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");
stats_page_views_repository();
utils_classes();

class StatsPageViewsService {
    public static function GetPageViews(SqlTimeSpecifier $since = new SqlTimeSpecifier(SQL_TIMESPECIFIER_UNIT_MONTH, 3), ?string $app = null): array
    {
        $results = StatsPageViewsRepository::GetPageViews($since, $app);

        return ArrayUtils::createArrayWithMissingDays(
            $results,
            'day',
            strtotime("-" . $since->getSql()),
            strtotime('now'),
            fn($time) => ['views' => 0, 'day' => date("Y-m-d", $time), 'desktop_views' => 0, 'mobile_views' => 0]
        );
    }

    public static function GetAveragePageLoadTime(SqlTimeSpecifier $since = new SqlTimeSpecifier(SQL_TIMESPECIFIER_UNIT_MONTH, 3), ?string $app = null): array
    {
        $results = StatsPageViewsRepository::GetAveragePageLoadtime($since, $app);

        return ArrayUtils::createArrayWithMissingDays(
            $results,
            'day',
            strtotime("-" . $since->getSql()),
            strtotime('now'),
            fn($time) => ['average_loadtime' => 0, 'day' => date("Y-m-d", $time), 'average_loadtime_desktop' => 0, 'average_loadtime_mobile' => 0]
        );
    }
}