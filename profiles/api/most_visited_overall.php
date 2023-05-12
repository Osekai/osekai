<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");

api_controller_base_classes();
profiles_visited_repository();

class MostVisitedOverallApiController extends ApiController
{
    public function get(): ApiResult {
        return new OkApiResult(ProfilesVisitedRepository::getMostVisited());
    }
}

ApiControllerExecutor::execute(new MostVisitedOverallApiController, new JsonApiResultSerializer);

?>