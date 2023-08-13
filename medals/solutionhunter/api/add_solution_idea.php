<?php

include_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");

include_once(__DIR__ . '/../php/services.php');
include_once(__DIR__ . '/../php/models.php');

json_validator();
api_controller_base_classes();

class AddSolutionIdeaDto {
    private function __construct(
        public readonly ?string $text,
        public readonly ?int $medalId,
    ) {}
    
    public static function fromJsonArray(array $jsonArray): AddSolutionIdeaDto
    {
        return new AddSolutionIdeaDto($jsonArray['text'], $jsonArray['medal_id']);
    }
}

class AddSolutionIdeaController extends ApiController {
    /**
     * @throws Exception
     */
    public function post(): ApiResult
    {
        if (!isset($_SERVER["CONTENT_TYPE"]) || $_SERVER['CONTENT_TYPE'] !== 'application/json')
            return new BadRequestApiResult;

        if (!loggedin())
            return new UnauthorizedResult();

        $body = JsonBodyReader::read();

        if (!JsonValidator::validateAssociativeArray($body, [
            "text" => (new JsonValidatorRule())->must_be_string(minLength: 1, maxLength: SOLUTION_TRACKER_MAX_LENGTH),
            "medal_id" => (new JsonValidatorRule())->must_be_int(),
        ])) {
            return new BadRequestApiResult("Invalid body");
        }

        $addSolutionIdeaDto = AddSolutionIdeaDto::fromJsonArray(JsonBodyReader::read());
        
        $result = Database::wrapInTransaction(function () use ($addSolutionIdeaDto) {
            return SolutionTrackerService::addSolutionIdea(
                SolutionIdea::create(
                    new SolutionTrackerText($addSolutionIdeaDto->text),
                    $addSolutionIdeaDto->medalId,
                    new Submitter(intval($_SESSION['osu']['id']))
                )
            );
        });

        return match ($result) {
            AddSolutionIdeaResult::Success => new OkApiResult(),
            AddSolutionIdeaResult::UserAlreadySubmittedSolutionIdea => new BadRequestApiResult("User already submitted an idea for this medal"),
            AddSolutionIdeaResult::SolutionTrackerNotEnabledForMedal => new BadRequestApiResult("The solution tracker is not enabled for this medal"),
        };      
    }
}

ApiControllerExecutor::execute(new AddSolutionIdeaController, new JsonApiResultSerializer);