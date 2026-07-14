<?php

namespace App\Event;

use App\Contract\AsyncMessageInterface;
use App\Enum\TaskAction;

final class TaskCreatedEvent extends BaseTaskEvent implements AsyncMessageInterface
{
    public function __construct(public ?int $taskId, public array $newData)
    {
        parent::__construct($taskId, TaskAction::CREATE->value, [], $newData);
    }
}
