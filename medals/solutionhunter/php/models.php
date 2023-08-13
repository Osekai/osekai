<?php

define("SOLUTION_TRACKER_MAX_LENGTH", 200);

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