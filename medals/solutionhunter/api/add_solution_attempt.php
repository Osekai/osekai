<?php

include_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");

include_once(__DIR__ . '/../php/services.php');
include_once(__DIR__ . '/../php/models.php');

json_validator();
api_controller_base_classes();

class AddSolutionAttemptDto {
    private function __construct(
        public readonly ?string $text,
        public readonly ?int $medalId
    ) {}
    
    public static function fromJsonArray(array $jsonArray): AddSolutionAttemptDto
    {
        return new AddSolutionAttemptDto($jsonArray['text'], $jsonArray['medal_id']);
    }
}

class AddSolutionAttemptController extends ApiController {
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

        $addSolutionAttemptDto = AddSolutionAttemptDto::fromJsonArray($body);

        $result = Database::wrapInTransaction(function () use ($addSolutionAttemptDto) {
            return SolutionTrackerService::addSolutionAttempt(
                SolutionAttempt::create(
                    new SolutionTrackerText($addSolutionAttemptDto->text),
                    $addSolutionAttemptDto->medalId,
                    new Submitter(intval($_SESSION['osu']['id']))
                )
            );
        });

        return match ($result) {
            AddSolutionAttemptResult::Success => new OkApiResult(),
            AddSolutionAttemptResult::UserAlreadySubmittedSolutionAttempt => new BadRequestApiResult("User already submitted an attempt for this medal"),
            AddSolutionAttemptResult::SolutionTrackerNotEnabledForMedal => new BadRequestApiResult("The solution tracker is not enabled for this medal"),
        };      
    }
}

ApiControllerExecutor::execute(new AddSolutionAttemptController, new JsonApiResultSerializer);