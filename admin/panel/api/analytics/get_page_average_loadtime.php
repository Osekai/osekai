<?php

api_controller_base_classes();
stats_page_views_service();

class GetPageLoadtimeApiController extends ApiController
{
    public function get(): ApiResult {
        $app = null;

        if (array_key_exists('app', $_GET))
            $app = $_GET['app'];

        return new OkApiResult(StatsPageViewsService::GetAveragePageLoadTime(
            new SqlTimeSpecifier(SQL_TIMESPECIFIER_UNIT_MONTH, 3), $app));
    }
}

ApiControllerExecutor::execute(new GetPageLoadtimeApiController, new JsonApiResultSerializer);
