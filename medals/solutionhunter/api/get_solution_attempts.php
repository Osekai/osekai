<?php

include_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");

include_once(__DIR__ . '/../php/services.php');
include_once(__DIR__ . '/../php/models.php');
include_once(__DIR__ . '/common.php');

json_validator();
api_controller_base_classes();

class SolutionAttemptDto {
    public function __construct(
        public readonly int $id,
        public readonly string $text,
        public readonly SubmitterDto $submitter,
        public readonly bool $works
    ) {}

    public function toArray(): array {
        return [
            "id" => $this->id,
            "text" => $this->text,
            "submitter" => $this->submitter->toArray(),
            "works" => $this->works
        ];
    }
}

class GetSolutionAttemptsController extends ApiController {
    /**
     * @throws Exception
     */
    public function get(): ApiResult
    {
        if (!loggedin())
            return new UnauthorizedResult();

        $medal_id = filter_var($_GET['medal_id'], FILTER_VALIDATE_INT);
        if ($medal_id === false)
            return new BadRequestApiResult("Invalid or missing medal_id");

        $offset = filter_var($_GET['offset'], FILTER_VALIDATE_INT);
        $limit = filter_var($_GET['limit'], FILTER_VALIDATE_INT);

        if ($offset === false)
            $offset = 0;

        if ($limit === false)
            $limit = PHP_INT_MAX;

        $ideas = SolutionTrackerService::getSolutionAttempts($medal_id, $offset, $limit);

        return new OkApiResult(array_map(function ($attempt) {
            return (new SolutionAttemptDto(
                $attempt->id,
                $attempt->text->asString(),
                new SubmitterDto($attempt->submitter->id, $attempt->submitter->username),
                $attempt->works)
            )->toArray();
        }, $ideas));
    }
}

ApiControllerExecutor::execute(new GetSolutionAttemptsController, new JsonApiResultSerializer);