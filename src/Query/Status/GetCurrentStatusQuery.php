<?php

namespace App\Query\Status;

use App\Entity\User;
use Symfony\Component\Security\Core\User\UserInterface;

final readonly class GetCurrentStatusQuery
{
    public function __construct(
        public UserInterface|User $user,
    ) { }
}
