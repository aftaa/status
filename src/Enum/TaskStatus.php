<?php

// src/Enum/TaskStatus.php
namespace App\Enum;

enum TaskStatus: string
{
    case COMPLETED = 'completed';
    case NOT_COMPLETED = 'not_completed';

    public function getLabel(): string
    {
        return match($this) {
            self::COMPLETED => '✅ Выполнена',
            self::NOT_COMPLETED => '❌ Не выполнена',
        };
    }
}
