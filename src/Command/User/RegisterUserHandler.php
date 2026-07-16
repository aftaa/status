<?php

namespace App\Command\User;

use App\Entity\User;
use App\Event\UserRegisteredEvent;
use App\Repository\UserRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsMessageHandler(bus: 'command.bus')]
final readonly class RegisterUserHandler
{
    public function __construct(
        private UserRepository $userRepository,
        private UserPasswordHasherInterface $passwordHasher,
        private MessageBusInterface $eventBus,
    ) {}

    /**
     * @throws ExceptionInterface
     */
    public function __invoke(RegisterUserCommand $command): void
    {
        $existing = $this->userRepository->findOneBy(['email' => $command->email]);
        if ($existing) {
            throw new \DomainException('User with this email already exists');
        }

        $user = new User();
        $user->setEmail($command->email);
        $user->setDisplayName($command->displayName);
        $user->setPassword(
            $this->passwordHasher->hashPassword($user, $command->plainPassword)
        );

        $this->userRepository->save($user);

        $this->eventBus->dispatch(new UserRegisteredEvent(
            userId: $user->getId(),
            email: $user->getEmail(),
        ));
    }
}
