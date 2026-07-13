<?php

namespace App\Query\Log;

use App\Dto\LogListQuery;

final readonly class GetLogListQuery
{
    public function __construct(
        public LogListQuery $requestQuery,
    ) {

    }
}
