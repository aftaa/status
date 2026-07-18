<?php

namespace App\Query\Status;

use App\Entity\Status;

class GetCurrentStatusHandler
{
    public function __construct() { }

    public function __invoke(GetCurrentStatusQuery $query): ?Status
    {
        return $query->user->getCurrentStatus();
    }
}
