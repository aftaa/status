<?php

namespace App\ValueObject;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class Email
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Email]
        private string $value,
    ) {
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function equals(Email $other): bool
    {
        return $this->value === $other->value;
    }
}
