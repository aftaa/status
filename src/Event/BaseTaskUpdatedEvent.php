<?php

namespace App\Event;

use App\Contract\AsyncMessageInterface;
use App\Enum\TaskAction;

final class BaseTaskUpdatedEvent extends BaseTaskEvent implements AsyncMessageInterface
{
    public function __construct(public ?int $taskId, public array $oldData, public array $newData)
    {
        parent::__construct($taskId, TaskAction::EDIT->value, $oldData, $newData);
    }
}
