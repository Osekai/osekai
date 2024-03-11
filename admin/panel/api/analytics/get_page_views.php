<?php

api_controller_base_classes();
stats_page_views_service();

class GetPageViewsApiController extends ApiController
{
    public function get(): ApiResult {
        $app = null;

        if (array_key_exists('app', $_GET))
            $app = $_GET['app'];

        return new OkApiResult(StatsPageViewsService::GetPageViews(
            new SqlTimeSpecifier(SQL_TIMESPECIFIER_UNIT_MONTH, 3), $app));
    }
}

ApiControllerExecutor::execute(new GetPageViewsApiController, new JsonApiResultSerializer);