<?php

class JsonValidatorRule {
    private array $rules;

    public function __construct() {
        $this->rules = array();
    }

    private function add_function_rule(\Closure $closure): void
    {
        $this->rules[] = $closure;
    }

    public function must_be_int(): JsonValidatorRule {
        $this->add_function_rule(function($v) { return is_int($v); });

        return $this;
    }

    public function must_be_string(?int $minLength = null, ?int $maxLength = null, $trim = true, $nonEmpty = true): JsonValidatorRule {
        $this->add_function_rule(function($v) use ($minLength, $maxLength, $trim, $nonEmpty) {
            if (!is_string($v))
                return false;

            $v = $trim ? trim($v) : $v;

            if ($nonEmpty && $v === "")
                return false;

            if (!isset($minLength) && !isset($maxLength))
                return true;

            $length = strlen($v);
            if (isset($minLength) && $length < $minLength)
                return false;

            if (isset($maxLength) && $length > $maxLength)
                return false;

            return true;
        });

        return $this;
    }

    public function validate($v): bool {
        foreach ($this->rules as $rule) {
            if (!$rule($v))
                return false;
        }

        return true;
    }
}

class JsonValidator {
    public static function validateAssociativeArray(array $array, array $rules): bool {
        foreach ($array as $key => $value) {
            if (isset($rules[$key]) && !$rules[$key]->validate($value))
                return false;
        }

        return true;
    }
}