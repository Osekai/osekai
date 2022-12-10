<?php

require_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");

api_controller_base_classes();
changelog_service();

class ChangelogUpdateApiController extends ApiController
{
    private static function throwExceptionIfDataIsInvalid(array $data) {
        if (!is_int($data['name']))
            throw new InvalidParameterException("Invalid 'name' field");

        if (!is_string($data['date']))
            throw new InvalidParameterException("Invalid 'date' field");

        if (!is_array($data['entries']))
            throw new InvalidParameterException("Entries field is not a valid array");

        foreach ($data['entries'] as $entry) {
            if (!is_string($entry['name']))
                throw new InvalidParameterException("Invalid entry 'name' field");

            if (!is_string($entry['user']))
                throw new InvalidParameterException("Invalid entry 'user' field");

            if (!is_string($entry['link']))
                throw new InvalidParameterException("Invalid entry 'link' field");
        
            if (!is_array($entry['tags']))
                throw new InvalidParameterException("Invalid entry 'tags' array");

            foreach ($entry['tags'] as $tag) {
                if (!is_string($tag))
                    throw new InvalidParameterException("Invalid tag in entry 'tags' field");
            }
        }
    }

    public function post(): ApiResult {
        if (!isset($_POST['key']) || $_POST['key'] != CHANGELOG_KEY)
            return new UnauthorizedResult;

        if (!isset($_POST['data']))
            return new BadArgumentsApiResult("No data");

        $data = json_decode($_POST['data'], flags: JSON_OBJECT_AS_ARRAY);
        ChangelogUpdateApiController::throwExceptionIfDataIsInvalid($data);
        ChangelogService::addChangelog($data['name'], $data['date'], $data['entries']);

        return new OkApiResult();
    }
}

ApiControllerExecutor::execute(new ChangelogUpdateApiController, new JsonApiResultSerializer);
