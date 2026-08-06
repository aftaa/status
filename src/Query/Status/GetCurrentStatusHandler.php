<?php

namespace App\Query\Status;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'query.bus')]
class GetCurrentStatusHandler
{
    public function __construct() { }

    public function __invoke(GetCurrentStatusQuery $query): array
    {
        return [
            'status' => $query->user->getCurrentStatus() ?? null,
            'statusTime' => $query->user->getStatusTime() ?? null,
        ];
    }
}
