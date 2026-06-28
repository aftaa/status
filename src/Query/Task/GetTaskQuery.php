<?php

namespace App\Query\Task;

readonly class GetTaskQuery
{
    public function __construct(public final int $taskId)
    {

    }
}
