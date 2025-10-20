<?php

namespace App\Model;

readonly class User
{
    public function __construct(
        public int $id,
        public string $name,
        public bool $checkedIn,
        public ?string $password = null,
        public string $role = 'user'
    ) {}
}
