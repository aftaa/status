<?php

namespace App\Enum;

enum TaskAction: string
{
    case CREATE = 'create';
    case EDIT = 'edit';
    case DELETE = 'delete';
}
