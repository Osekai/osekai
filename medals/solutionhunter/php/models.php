<?php

const SOLUTION_TRACKER_MAX_LENGTH = 200;

final class SolutionTrackerText {
    private string $value;
    
    public function __construct(string $value) 
    {
        if (strlen($value) >= 200)
            throw new InvalidArgumentException("string is too long (max length: " . SOLUTION_TRACKER_MAX_LENGTH . ")");

        $value = trim($value);

        if ($value === "")
            throw new InvalidArgumentException("only whitespace string provided");

        $this->value = $value;
    }

    public function asString(): string 
    {
        return $this->value;
    }
}


class Submitter {
    public function __construct(
        public readonly int $id,
        public readonly ?string $username = null
    ) {}
}

class SolutionIdea {
    public static function create(SolutionTrackerText $text, int $medalId, Submitter $submitter): SolutionIdea {
        return new SolutionIdea(0, $text, $medalId, $submitter);
    }

    public function __construct(
        public readonly int $id,
        public readonly SolutionTrackerText $text,
        public readonly int $medalId,
        public readonly Submitter $submitter
    ) {}
}

class SolutionAttempt {
    public static function create(SolutionTrackerText $text, int $medalId, Submitter $submitter): SolutionAttempt {
        return new SolutionAttempt(0, $text, $medalId, $submitter, false);
    }

    public function __construct(
        public readonly int $id,
        public readonly SolutionTrackerText $text,
        public readonly int $medalId,
        public readonly Submitter $submitter,
        public readonly bool $works
    ) {}
}