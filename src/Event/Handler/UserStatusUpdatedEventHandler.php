<?php

namespace App\Event\Handler;

use App\Event\UserStatusUpdatedEvent;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'event.bus')]
final readonly class UserStatusUpdatedEventHandler
{
    public function __construct(
        private HubInterface $mercureHub,
    ) {}

    public function __invoke(UserStatusUpdatedEvent $event): void
    {
        $update = new Update(
            topics: ['user/' . $event->status->userId . '/status'],
            data: json_encode([
                'userId' => $event->status->userId,
                'status' => [
                    'slug' => $event->status->slug,
                    'name' => $event->status->name,
                    'color' => $event->status->color,
                    'bgColor' => $event->status->bgColor,
                    'iconUrl' => $event->status->iconUrl,
                ]
            ])
        );

        $this->mercureHub->publish($update);
    }
}
