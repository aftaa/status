<?php

namespace App\Command\User;

final readonly class UpdateUserStatusCommand
{
    public function __construct(
        public int $userId,
        public string $statusSlug,
    ) {}
}
