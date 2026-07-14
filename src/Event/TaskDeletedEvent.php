<?php

namespace App\Event;

use App\Contract\AsyncMessageInterface;
use App\Enum\TaskAction;

final class TaskDeletedEvent extends BaseTaskEvent implements AsyncMessageInterface
{
    public function __construct(public ?int $taskId, public array $oldData)
    {
        parent::__construct($taskId, TaskAction::DELETE->value, $oldData, []);
    }
}
