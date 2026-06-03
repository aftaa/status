<?php

namespace App\Enum;

enum TaskOrder: string
{
    case ASC = 'asc';
    case DESC = 'desc';

    final public const self DEFAULT = self::ASC;

    public function getLabel(): string
    {
        return match ($this) {
            self::ASC => 'По возрастанию',
            self::DESC => 'По убыванию',
        };
    }

    public function isAsc(): bool
    {
        return $this === self::ASC;
    }

    public function isDesc(): bool
    {
        return $this === self::DESC;
    }

    public function toggle(): self
    {
        return $this->isAsc() ? self::DESC : self::ASC;
    }
}
