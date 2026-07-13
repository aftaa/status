<?php

namespace App\Dto;

use App\ValueObject\Pagination\Limit;
use App\ValueObject\Pagination\Page;

final readonly class LogListQuery
{
    public function __construct(
        public Page  $page,
        public Limit $limit,
    ) {

    }
}
