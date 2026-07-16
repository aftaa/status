<?php

namespace App\Event;

use App\Contract\AsyncMessageInterface;

final readonly class UserRegisteredEvent implements AsyncMessageInterface
{
    public function __construct(
        public int $userId,
        public string $email,
    ) {}
}
