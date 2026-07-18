<?php

namespace App\Query\Status;

use App\Entity\User;

final readonly class GetCurrentStatusQuery
{
    public function __construct(
        public User $user,
    ) { }
}
