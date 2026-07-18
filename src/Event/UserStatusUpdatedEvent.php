<?php

namespace App\Event;

use App\Contract\AsyncMessageInterface;
use App\Dto\UserStatusDto;

final readonly class UserStatusUpdatedEvent implements AsyncMessageInterface
{
    public function __construct(
        public UserStatusDto $status,
    ) {}
}
