<?php

namespace App\Factory;

use App\Specification\SpecificationInterface;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;

class SpecificationFactory
{
    private array $specifications = [];

    public function __construct(
        #[TaggedIterator('app.task_specification')] iterable $specifications
    ) {
        foreach ($specifications as $specification) {
            $this->specifications[$specification->getKey()] = $specification;
        }
    }

    public function getSpecification(?string $key): ?SpecificationInterface
    {
        if ($key === null || $key === 'all') {
            return null;
        }

        return $this->specifications[$key] ?? null;
    }
}
