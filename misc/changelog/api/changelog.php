<?php

require_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");

api_controller_base_classes();
changelog_service();

class ChangelogApiController extends ApiController
{
    public function get(): ApiResult {
        if (!isset($_GET['name']))
            return new OkApiResult(ChangelogService::getChangelogs());

        $name = filter_var($_GET['name'], FILTER_VALIDATE_INT);

        if (!$name)
            return new BadArgumentsApiResult("name is not a valid integer");
        
        $changelog = ChangelogService::getChangelogByName($_GET['name']);
        if ($changelog == null)
            return new ResourceNotFoundApiResult();

        return new OkApiResult($changelog);
    }
}

ApiControllerExecutor::execute(new ChangelogApiController, new JsonApiResultSerializer);
