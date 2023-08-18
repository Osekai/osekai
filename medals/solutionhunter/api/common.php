<?php

class SubmitterDto {
    public function __construct(
        public readonly int $userId,
        public readonly ?string $username,
    ) {}

    public function toArray(): array {
        return [
            "user_id" => $this->userId,
            "username" => $this->username
        ];
    }
}