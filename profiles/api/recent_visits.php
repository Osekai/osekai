<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");

api_controller_base_classes();
profiles_visited_repository();

class RecentlyVisitedApiController extends ApiController
{
    public function get(): ApiResult {
        if (!loggedin()) {
            return new UnauthorizedResult;
        }

        return new OkApiResult(ProfilesVisitedRepository::getRecentlyVisited($_SESSION['osu']['id'], 10));
    }
}

ApiControllerExecutor::execute(new RecentlyVisitedApiController, new JsonApiResultSerializer);