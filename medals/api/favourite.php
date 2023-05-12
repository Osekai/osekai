<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");

api_controller_base_classes();
json_validator();

class FavouriteMedalApiController extends ApiController
{
    public function get(): ApiResult {
        if (!isset($_SESSION['osu']['id']) || isRestricted()) {
            return new UnauthorizedResult;
        }

        $medals = array_map(static function($result) {
            return $result['medal_id'];
        }, Database::execSelect("SELECT medal_id FROM FavouriteMedals WHERE user_id = ?", 'i', [$_SESSION['osu']['id']]));

        return new OkApiResult($medals);
    }

    public function put(): ApiResult {
        if (!isset($_SESSION['osu']['id']) || isRestricted()) {
            return new UnauthorizedResult;
        }

        $requestJson = JsonBodyReader::read();
        
        if (!JsonValidator::validate_associative_array($requestJson, [
            'medal_id' => (new JsonValidatorRule())->must_be_int()
        ])) {
            return new BadArgumentsApiResult(["message" => "Invalid medal_id"]);
        }

        $medalId = $requestJson['medal_id'];

        if (Database::execSelectFirstOrNull("SELECT EXISTS (SELECT * FROM Medals WHERE medalid = ?) as medal_exists", 'i', [$medalId])['medal_exists'])
            return new BadArgumentsApiResult(["message" => "Medal not found"]);

        Database::execOperation("REPLACE INTO FavouriteMedals VALUES (?, ?)", 'ii', [$_SESSION['osu']['id'], $medalId]);
        return new OkApiResult();
    }

    public function delete(): ApiResult {
        if (!isset($_SESSION['osu']['id']) || isRestricted()) {
            return new UnauthorizedResult;
        }

        $requestJson = JsonBodyReader::read();
        
        if (!JsonValidator::validate_associative_array($requestJson, [
            'medal_id' => (new JsonValidatorRule())->must_be_int()
        ])) {
            return new BadArgumentsApiResult(["message" => "Invalid medal_id"]);
        }

        $medalId = $requestJson['medal_id'];

        if (Database::execSelectFirstOrNull("SELECT EXISTS (SELECT * FROM Medals WHERE medalid = ?) as medal_exists", 'i', [$medalId])['medal_exists'])
            return new BadArgumentsApiResult(["message" => "Medal not found"]);

        Database::execOperation("DELETE FROM FavouriteMedals WHERE user_id = ? AND medal_id = ?", 'ii', [$_SESSION['osu']['id'], $medalId]);
        return new OkApiResult();
    }
}

ApiControllerExecutor::execute(new FavouriteMedalApiController, new JsonApiResultSerializer);
