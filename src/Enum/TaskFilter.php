<?php

namespace App\Enum;

enum TaskFilter: string
{
    case ALL = 'all';
    case COMPLETED = 'completed';
    case NOT_COMPLETED = 'not_completed';

    public final const self DEFAULT = self::ALL;

    public function getLabel(): string
    {
        return match ($this) {
            self::ALL => 'Все',
            self::COMPLETED => 'Выполненные',
            self::NOT_COMPLETED => 'Невыполненные',
        };
    }

    public function isAll(): bool
    {
        return $this === self::ALL;
    }

    public function isCompleted(): bool
    {
        return $this === self::COMPLETED;
    }

    public function isNotCompleted(): bool
    {
        return $this === self::NOT_COMPLETED;
    }
}
