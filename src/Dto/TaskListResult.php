<?php

namespace App\Dto;

readonly class TaskListResult
{
    public function __construct(
        public array         $tasks,
        public TaskListQuery $query,
    )
    {
    }
}
