<?php

namespace App\ValueObject\Pagination;

class Page
{
    public private(set) int $number {
        set (int $number) {
            if ($number < 1) {
                throw new \InvalidArgumentException('Page must be >= 1');
            }
            $this->number = $number;
        }
    }

    public function __construct(int $number = 1)
    {
        $this->number = $number;
    }

    public function __toString(): string
    {
        return (string) $this->number;
    }
}
