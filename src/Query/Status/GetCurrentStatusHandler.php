<?php

namespace App\Query\Status;

use App\Entity\Status;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'query.bus')]
class GetCurrentStatusHandler
{
    public function __construct() { }

    public function __invoke(GetCurrentStatusQuery $query): ?Status
    {
        return $query->user->getCurrentStatus();
    }
}
