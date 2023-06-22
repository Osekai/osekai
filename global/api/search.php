<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");

api_controller_base_classes();
search_service();

class SearchApiController extends ApiController
{
    private static function ConvertSearchUserResultToSearchApiResult(SearchUserResult $result): array {
        return [
            "url" => "/profiles/?user=" . $result->getUserId(),
            "name" => $result->getUsername(),
            "img" => $result->getAvatarUrl()
        ];
    }

    private static function ConvertSearchMedalResultToSearchApiResult(SearchMedalResult $result): array {
        return [
            "url" => "/medals/?medal=" . $result->getMedalName(),
            "name" => $result->getMedalName(),
            "img" => $result->getIconUrl()
        ];
    }

    private static function ConvertSearchSnapshotVersionResultToSearchApiResult(SearchSnapshotVersionResult $result): array {
        return [
            "url" => "/snapshots/?version=" . $result->getSnapshotVersionId(),
            "name" => $result->getSnapshotVersionName(),
            "img" => null
        ];
    }

    public function post(): ApiResult
    {
        $query = $_POST['query'];
        $type = $_POST['type'];

        if (!loggedin() || isRestricted())
            return new UnauthorizedResult;

        if (!isset($query) && !isset($type))
            return new BadArgumentsApiResult;
        
        switch ($type) {
            case "profiles":
                $results = SearchService::searchUser($query);
                $results = array_map(static function($r) { return Self::ConvertSearchUserResultToSearchApiResult($r); }, $results);
                break;

            case "medals":
                $results = SearchService::searchMedal($query);
                $results = array_map(static function($r) { return Self::ConvertSearchMedalResultToSearchApiResult($r); }, $results);
                break;

            case "snapshots":
                $results = SearchService::searchSnapshotVersion($query);
                $results = array_map(static function($r) { return Self::ConvertSearchSnapshotVersionResultToSearchApiResult($r); }, $results);
                break;

            default:
                return new BadArgumentsApiResult;
        }

        return new OkApiResult($results);
    }
}

ApiControllerExecutor::execute(new SearchApiController, new JsonApiResultSerializer);