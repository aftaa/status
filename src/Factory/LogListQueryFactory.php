<?php

namespace App\Factory;

use App\Dto\LogListQuery;
use App\ValueObject\Pagination\Limit;
use App\ValueObject\Pagination\Page;
use Symfony\Component\HttpFoundation\Request;

class LogListQueryFactory
{
    const int DEFAULT_PAGE = 1;
    const int DEFAULT_LIMIT = 100;

    public function fromRequest(Request $request): LogListQuery
    {
        return new LogListQuery(
            new Page(max(self::DEFAULT_PAGE, $request->query->getInt('page', 1))),
            new Limit(max(self::DEFAULT_LIMIT, $request->query->getInt('limit', 20))),
        );
    }
}
