<?php

namespace App\Command\User;

readonly class RegisterUserCommand
{
    public function __construct(
        public string $email,
        public string $plainPassword,
        public string $displayName,
    ) {
    }
}
