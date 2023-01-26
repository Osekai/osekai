<?php

require_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");

api_controller_base_classes();
osekai_http_request();

class GithubLabelProxyController extends ApiController
{
    public function get(): ApiResult {
        $cachedResult = Caching::getCache("github_label_proxy_cache");

        if ($cachedResult != null) {
            return new OkApiResult($cachedResult, doNotEncode: true);
        }
        
        $result = (new OsekaiHttpRequest("https://api.github.com/repos/Osekai/osekai/labels"))->get();

        if ($result->getStatusCode() != 200) {
            error_log($result->getResult());
            Logging::PutLog("Non Ok status from GitHub API: " . $result->getResult());
            return new UnknownErrorResult("The GitHub API returned an non Ok status.");
        }

        Caching::saveCache("github_label_proxy_cache", 604800, $result->getResult()); // It expires after one week
        return new OkApiResult($result->getResult(), doNotEncode: true);
    }
}

ApiControllerExecutor::execute(new GithubLabelProxyController, new JsonApiResultSerializer());
