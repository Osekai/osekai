<?php

require_once "models.php";

enum AddSolutionIdeaResult {
    case Success;
    case UserAlreadySubmittedSolutionIdea;
    case SolutionTrackerNotEnabledForMedal;
}

enum AddSolutionAttempt {
    case Success;
    case UserAlreadySubmittedSolutionAttempt;
    case SolutionTrackerNotEnabledForMedal;
}

final class SolutionTrackerService {
    private function __construct() {}

    public static function addSolutionIdea(int $submitterId, int $medalId, SolutionTrackerText $text): AddSolutionIdeaResult {
        $queryResult = Database::execSelectFirstOrNull(
            "SELECT EXISTS (SELECT * FROM SolutionTracker WHERE UserId = ? AND Type = 1) as `solutionAlreadySubmitted`, " .
            "EXISTS (SELECT * FROM Medals WHERE medalid = ? AND solutiontrackerenabled = 1) as `solutionTrackerEnabledForMedal`;", "ii", [$submitterId, $medalId]);

        $solutionAlreadySubmitted = $queryResult['solutionAlreadySubmitted'];
        $solutionTrackerEnabledForMedal = $queryResult['solutionTrackerEnabledForMedal'];

        if ($solutionAlreadySubmitted)
            return AddSolutionIdeaResult::UserAlreadySubmittedSolutionIdea;

        if (!$solutionTrackerEnabledForMedal)
            return AddSolutionIdeaResult::SolutionTrackerNotEnabledForMedal;

        Database::execOperation("INSERT INTO SolutionTracker (`MedalId`, `UserId`, `Text`, `Type`, `Status`) VALUES (?, ?, ?, 1, 1)",
            "iis",
            [$medalId, $submitterId, $text->asString()]);

        return AddSolutionIdeaResult::Success;
    }

    /**
     * @throws Exception
     */
    public static function addSolutionAttempt(int $submitterId, int $medalId, SolutionTrackerText $text): AddSolutionAttempt {
        $queryResult = Database::execSelectFirstOrNull(
            "SELECT EXISTS (SELECT * FROM SolutionTracker WHERE UserId = ? AND Type = 2) as `solutionAlreadySubmitted`, " .
            "EXISTS (SELECT * FROM Medals WHERE medalid = ? AND solutiontrackerenabled = 1) as `solutionTrackerEnabledForMedal`;", "ii", [$submitterId, $medalId]);

        $solutionAlreadySubmitted = $queryResult['solutionAlreadySubmitted'];
        $solutionTrackerEnabledForMedal = $queryResult['solutionTrackerEnabledForMedal'];

        if ($solutionAlreadySubmitted)
            return AddSolutionAttempt::UserAlreadySubmittedSolutionAttempt;

        if (!$solutionTrackerEnabledForMedal)
            return AddSolutionAttempt::SolutionTrackerNotEnabledForMedal;

        Database::execOperation("INSERT INTO SolutionTracker (`MedalId`, `UserId`, `Text`, `Type`, `Status`) VALUES (?, ?, ?, 2, 0)",
            "iis",
            [$medalId, $submitterId, $text->asString()]);

        return AddSolutionAttempt::Success;
    }
}