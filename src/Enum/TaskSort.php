<?php

namespace App\Enum;

enum TaskSort: string
{
    case CREATED_AT = 'created_at';
    case NAME = 'name';
    case IS_COMPLETED = 'is_completed';
    case ID = 'id';

    public final const self DEFAULT = self::CREATED_AT;

    public function getLabel(): string
    {
        return match ($this) {
            self::CREATED_AT => 'По дате создания',
            self::NAME => 'По названию',
            self::IS_COMPLETED => 'По статусу',
            self::ID => 'По ИД',
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

    public function isId(): bool
    {
        return $this === self::ID;
    }
}
