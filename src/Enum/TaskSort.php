<?php

namespace App\Enum;

enum TaskSort: string
{
    case CREATED_AT = 'created_at';
    case NAME = 'name';
    case IS_COMPLETED = 'is_completed';

    public function getLabel(): string
    {
        return match ($this) {
            self::CREATED_AT => 'По дате создания',
            self::NAME => 'По названию',
            self::IS_COMPLETED => 'По статусу',
        };
    }

    public function isCreatedAt(): bool
    {
        return $this === self::CREATED_AT;
    }

    public function isName(): bool
    {
        return $this === self::NAME;
    }

    public function isCompleted(): bool
    {
        return $this === self::IS_COMPLETED;
    }
}
