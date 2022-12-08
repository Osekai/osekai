<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");

api_controller_base_classes();
goals_service();

class GoalsApiController extends ApiController
{
    public function post(): ApiResult {
        if (!isset($_SESSION['osu']['id']) || isRestricted())
            return new UnauthorizedResult;

        if (isset($_POST['Value'])) {
            GoalsService::addGoal($_SESSION['osu']['id'], $_POST['Type'], $_POST['Gamemode'], $_POST['Value']);
            return new OkApiResult("Success!");
        }

        if (isset($_POST['GoalID'])) {
            GoalsService::removeGoal($_POST['GoalID'], $_SESSION['osu']['id']);
            return new OkApiResult("Success!");
        }

        if (isset($_POST['ClaimID'])) {
            GoalsService::claimGoal($_POST['ClaimID'], $_SESSION['osu']['id']);
            return new OkApiResult("Success!");
        }

        return new NotImplementedApiResult;
    }
}

ApiControllerExecutor::execute(new GoalsApiController, new JsonApiResultSerializer);

?>