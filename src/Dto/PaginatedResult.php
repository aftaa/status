<?php

namespace App\Dto;

readonly class PaginatedResult
{
    public function __construct(
        public array $items,
        public int   $currentPage,
        public int   $totalItems,
        public int   $itemsPerPage,
    ) {}

    public function getTotalPages(): int
    {
        return (int) ceil($this->totalItems / $this->itemsPerPage);
    }
}
