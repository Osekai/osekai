<?php

include_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");

api_controller_base_classes();
profiles_visited_repository();

class MostVisitedApiController extends ApiController
{
    public function get(): ApiResult {
        return new OkApiResult(ProfilesVisitedRepository::getMostVisited(10, new SqlTimeSpecifier(SQL_TIMESPECIFIER_UNIT_WEEK, 2)));
    }
}

ApiControllerExecutor::execute(new MostVisitedApiController, new JsonApiResultSerializer);

?>