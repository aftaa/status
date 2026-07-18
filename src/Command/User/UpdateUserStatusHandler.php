<?php

namespace App\Command\User;

use App\Dto\UserStatusDto;
use App\Event\UserStatusUpdatedEvent;
use App\Repository\StatusRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler(bus: 'command.bus')]
final readonly class UpdateUserStatusHandler
{
    public function __construct(
        private UserRepository $userRepository,
        private StatusRepository $statusRepository,
        private MessageBusInterface $eventBus,
    ) {}

    /**
     * @throws ExceptionInterface
     */
    public function __invoke(UpdateUserStatusCommand $command): UserStatusDto
    {
        $user = $this->userRepository->find($command->userId);
        if (!$user) {
            throw new \InvalidArgumentException('User not found');
        }

        $status = $this->statusRepository->findOneBy(['slug' => $command->statusSlug]);
        if (!$status) {
            throw new \InvalidArgumentException('Status not found');
        }

        $user->setCurrentStatus($status);
        $this->userRepository->save($user);

        $dto = new UserStatusDto(
            userId: $user->getId(),
            slug: $status->getSlug(),
            name: $status->getName(),
            color: $status->getColor(),
            bgColor: $status->getBgColor(),
            iconUrl: $status->getIconUrl(),
        );

        $this->eventBus->dispatch(new UserStatusUpdatedEvent($dto));

        return $dto;
    }
}
