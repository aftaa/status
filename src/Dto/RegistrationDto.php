<?php

namespace App\Dto\User;

use Symfony\Component\Validator\Constraints as Assert;

class RegistrationDto
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Email]
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
