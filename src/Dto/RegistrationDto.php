<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class RegistrationDto
{
    public function __construct(
        public string $email = '',

        #[Assert\NotBlank]
        #[Assert\Length(min: 6, max: 4096)]
        public string $plainPassword = '',

        #[Assert\NotBlank]
        #[Assert\Length(min: 2, max: 100)]
        public string $displayName = '',

        #[Assert\IsTrue(message: 'You must agree to the terms')]
        public bool $agreeTerms = false,
    ) {}
}
