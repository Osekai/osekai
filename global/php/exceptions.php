<?php

class InvalidParameterException extends Exception {
    public function __construct(string $message = "") {
        parent::__construct($message);
    }
}

class InvalidOperationException extends Exception {
    public function __construct(string $message = "") {
        parent::__construct($message);
    }
}

class ResourceNotFoundException extends Exception {
    public function __construct(string $message = "") {
        parent::__construct($message);
    }
}