<?php

require_once "models.php";

enum AddSolutionIdeaResult {
    case Success;
    case UserAlreadySubmittedSolutionIdea;
    case SolutionTrackerNotEnabledForMedal;
}

enum AddSolutionAttemptResult {
    case Success;
    case UserAlreadySubmittedSolutionAttempt;
    case SolutionTrackerNotEnabledForMedal;
}


final class SolutionTrackerService {
    private function __construct() {}

    public static function addSolutionIdea(SolutionIdea $solutionIdea): AddSolutionIdeaResult {
        $queryResult = Database::execSelectFirstOrNull(
            "SELECT EXISTS (SELECT * FROM SolutionTracker WHERE UserId = ? AND Type = 1) as `solutionAlreadySubmitted`, " .
            "EXISTS (SELECT * FROM Medals WHERE medalid = ? AND solutiontrackerenabled = 1) as `solutionTrackerEnabledForMedal`;",
            "ii",
            [$solutionIdea->submitter->id, $solutionIdea->medalId]);

        $solutionAlreadySubmitted = $queryResult['solutionAlreadySubmitted'];
        $solutionTrackerEnabledForMedal = $queryResult['solutionTrackerEnabledForMedal'];

        if ($solutionAlreadySubmitted)
            return AddSolutionIdeaResult::UserAlreadySubmittedSolutionIdea;

        if (!$solutionTrackerEnabledForMedal)
            return AddSolutionIdeaResult::SolutionTrackerNotEnabledForMedal;

        Database::execOperation("INSERT INTO SolutionTracker (`MedalId`, `UserId`, `Text`, `Type`, `Status`) VALUES (?, ?, ?, 1, 0)",
            "iis",
            [$solutionIdea->medalId, $solutionIdea->submitter->id, $solutionIdea->text->asString()]);

        return AddSolutionIdeaResult::Success;
    }

    /**
     * @throws Exception
     */
    public static function addSolutionAttempt(SolutionAttempt $solutionAttempt): AddSolutionAttemptResult {
        $queryResult = Database::execSelectFirstOrNull(
            "SELECT EXISTS (SELECT * FROM SolutionTracker WHERE UserId = ? AND Type = 2) as `solutionAlreadySubmitted`, " .
            "EXISTS (SELECT * FROM Medals WHERE medalid = ? AND solutiontrackerenabled = 1) as `solutionTrackerEnabledForMedal`;",
            "ii",
            [$solutionAttempt->submitter->id, $solutionAttempt->medalId]);

        $solutionAlreadySubmitted = $queryResult['solutionAlreadySubmitted'];
        $solutionTrackerEnabledForMedal = $queryResult['solutionTrackerEnabledForMedal'];

        if ($solutionAlreadySubmitted)
            return AddSolutionAttemptResult::UserAlreadySubmittedSolutionAttempt;

        if (!$solutionTrackerEnabledForMedal)
            return AddSolutionAttemptResult::SolutionTrackerNotEnabledForMedal;

        Database::execOperation("INSERT INTO SolutionTracker (`MedalId`, `UserId`, `Text`, `Type`, `Status`) VALUES (?, ?, ?, 2, 1)",
            "iis",
            [$solutionAttempt->medalId, $solutionAttempt->submitter->id, $solutionAttempt->text->asString()]);

        return AddSolutionAttemptResult::Success;
    }

    public static function getSolutionIdeas(int $medalId, int $offset = 0, int $limit = PHP_INT_MAX): array {
        $connection = Database::getConnection();

        $offset = $connection->real_escape_string(strval($offset));
        $limit = $connection->real_escape_string(strval($limit));

        $results = Database::execSelect(
            "SELECT s.*, r.`name` as `Username` FROM SolutionTracker s LEFT JOIN Ranking r ON r.Id = s.UserId WHERE Type = 1 AND MedalId = ? " .
            "LIMIT $limit OFFSET $offset",
            "i", [$medalId]);

        return array_map(function($v) {
            return new SolutionIdea(intval($v['Id']),
                new SolutionTrackerText($v['Text']), intval($v['MedalId']),
                new Submitter(intval($v['UserId']), isset($v['Username']) ? strval($v['Username']) : null));
        }, $results);
    }

    public static function getSolutionAttempts(int $medalId, int $offset = 0, int $limit = PHP_INT_MAX): array {
        $connection = Database::getConnection();

        $offset = $connection->real_escape_string(strval($offset));
        $limit = $connection->real_escape_string(strval($limit));

        $results = Database::execSelect(
            "SELECT s.*, r.`name` as `Username` FROM SolutionTracker s LEFT JOIN Ranking r ON r.Id = s.UserId WHERE Type = 2 AND MedalId = ? " .
            "LIMIT $limit OFFSET $offset",
            "i", [$medalId]);

        return array_map(function($v) {
            return new SolutionAttempt(intval($v['Id']),
                new SolutionTrackerText($v['Text']), intval($v['MedalId']),
                new Submitter(intval($v['UserId']), isset($v['Username']) ? strval($v['Username']) : null),

                match (intval($v['Status'])) {
                    1 => false,
                    2 => true
                }
            );
        }, $results);
    }
}