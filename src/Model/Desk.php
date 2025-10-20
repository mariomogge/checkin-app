<?php

namespace App\Model;

readonly class Desk
{
    public function __construct(
        public int $id,
        public string $location,
        public bool $isAvailable
    ) {}
}
