<?php

namespace App\ValueObject\Pagination;

class Limit implements \Stringable
{
    public private(set) int $value {
        set (int $value) {
            if ($value < 1 || $value > 100) {
                throw new \InvalidArgumentException('Limit must be between 1 and 100');
            }
            $this->value = $value;
        }
    }

    public function __construct(int $value = 10)
    {
        $this->value = $value;
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }
}
