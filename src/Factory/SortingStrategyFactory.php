<?php

namespace App\Factory;

use App\Strategy\SortingStrategyInterface;

class SortingStrategyFactory
{
    /**
     * @var SortingStrategyInterface[]
     */
    private array $strategies = [];

    public function __construct(iterable $strategies)
    {
        foreach ($strategies as $strategy) {
            $this->strategies[$strategy->getKey()] = $strategy;
        }
    }

    public function getStrategy(string $type): SortingStrategyInterface
    {
        return $this->strategies[$type] ?? $this->strategies['created_at'];
    }
}
