<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");

include_once(__DIR__ . '/../php/services.php');
include_once(__DIR__ . '/../php/models.php');
include_once(__DIR__ . '/common.php');

json_validator();
api_controller_base_classes();

class SolutionIdeaDto {
    public function __construct(
        public readonly int $id,
        public readonly string $text,
        public readonly SubmitterDto $submitter
    ) {}

    public function toArray(): array {
        return [
            "id" => $this->id,
            "text" => $this->text,
            "submitter" => $this->submitter->toArray()
        ];
    }
}

class GetSolutionIdeasController extends ApiController {
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

        $ideas = SolutionTrackerService::getSolutionIdeas($medal_id, $offset, $limit);

        return new OkApiResult(array_map(function ($idea) {
            return (new SolutionIdeaDto(
                $idea->id,
                $idea->text->asString(),
                new SubmitterDto($idea->submitter->id, $idea->submitter->username))
            )->toArray();
        }, $ideas));
    }
}

ApiControllerExecutor::execute(new GetSolutionIdeasController, new JsonApiResultSerializer);