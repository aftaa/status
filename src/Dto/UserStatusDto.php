<?php

namespace App\Dto;

final readonly class UserStatusDto
{
    public function __construct(
        public int     $userId,
        public string  $slug,
        public string  $name,
        public string  $color,
        public string  $bgColor,
        public ?string $iconUrl,
    ) {
    }
}
