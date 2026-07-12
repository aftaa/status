<?php

namespace App\Event;

use App\Contract\AsyncMessageInterface;
use App\Dto\TaskEvent;

readonly class TaskChangedEvent implements AsyncMessageInterface
{
    public function __construct(public final TaskEvent $event)
    {

    }
}
