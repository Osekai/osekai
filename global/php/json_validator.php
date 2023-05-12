<?php

class JsonValidatorRule {
    private array $rules;

    public function __construct() {
        $this->rules = array();
    }

    private function add_function_rule(\Closure $closure) {
        array_push($this->rules, $closure);
    }

    public function must_be_int(): JsonValidatorRule {
        $this->add_function_rule(function($v) { return is_int($v); });

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
    public static function validate_associative_array(array $array, array $rules): bool {
        foreach ($array as $key => $value) {
            if (isset($rules[$key]) && !$rules[$key]->validate($value))
                return false;
        }

        return true;
    }
}