<?php

namespace App\Event\Handler;

use App\Event\UserRegisteredEvent;
use App\Security\EmailVerifier;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use App\Repository\UserRepository;

#[AsMessageHandler(bus: 'event.bus')]
final readonly class UserRegisteredEventHandler
{
    public function __construct(
        private UserRepository $userRepository,
        private EmailVerifier $emailVerifier,
    ) {}

    /**
     * @throws \Throwable
     */
    public function __invoke(UserRegisteredEvent $event): void
    {
        $user = $this->userRepository->find($event->userId);
        if (!$user) {
            $this->logger->error('User not found for email confirmation', [
                'userId' => $event->userId,
            ]);
            return;
        }

        try {
            $this->emailVerifier->sendEmailConfirmation(
                'app_verify_email',
                $user,
                (new TemplatedEmail())
                    ->from(new Address('after@aftaa.ru', 'after'))
                    ->to($event->email)
                    ->subject('Please Confirm your Email')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );
        } catch (\Throwable $e) {
            $this->logger->error('Failed to send confirmation email', [
                'userId' => $event->userId,
                'email' => $event->email,
                'error' => $e->getMessage(),
            ]);

            // Можно повторно отправить или оставить для ручного разбора
            throw $e;
        }
    }
}
