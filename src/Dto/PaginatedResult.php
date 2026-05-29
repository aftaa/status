<?php

namespace App\Dto;

class PaginatedResult
{
    public function __construct(
        public readonly array $items,
        public readonly int $currentPage,
        public readonly int $totalItems,
        public readonly int $itemsPerPage,
    ) {}

    public function getTotalPages(): int
    {
        return (int) ceil($this->totalItems / $this->itemsPerPage);
    }
}
