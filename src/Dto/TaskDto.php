<?php

namespace App\Dto;

use App\Enum\TaskStatus;
use Symfony\Component\Validator\Constraints as Assert;

final class TaskDto
{
    public function __construct(
        #[Assert\NotBlank(message: 'TaskDto.name.not_blank')]
        #[Assert\Length(min: 3, max: 255, minMessage: 'TaskDto.name.length', maxMessage: 'TaskDto.name.length')]
        public string     $name = '',

        public TaskStatus $status = TaskStatus::NOT_COMPLETED,
    )
    {
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'status' => $this->status->value,
        ];
    }
}
