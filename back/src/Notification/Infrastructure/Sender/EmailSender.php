<?php

namespace App\Notification\Infrastructure\Sender;

use App\Notification\Domain\Model\Notification;
use App\Notification\Domain\Model\NotificationDispatchResult;
use App\Notification\Domain\Model\NotificationStatus;
use App\Notification\Domain\Model\NotificationType;
use App\Notification\Infrastructure\Renderer\NotificationRenderer;
use App\Notification\Infrastructure\Renderer\NotificationSubjectGenerator;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

/**
 * @author Wilhelm Zwertvaegher
 */
final readonly class EmailSender implements NotificationSender
{

    private const string NAME = 'email';

    public function __construct(
        private MailerInterface             $mailer,
        private NotificationRenderer         $renderer,
        private NotificationRenderer         $textRenderer,
        private NotificationSubjectGenerator $subjectGenerator
    ) {
    }

    public function supports(Notification $notification): bool
    {
        return in_array($notification->getType(), [NotificationType::WELCOME]);
    }

    public function send(Notification $notification): NotificationSenderResult
    {
        $content = $this->renderer->render($notification, $this);

        $email = new Email()
            // TODO : this should be set as a global parameter in services.yaml
            ->from('hello@example.com')
            ->to($notification->getIdentityInfo()->getEmail())
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject($this->subjectGenerator->generate($notification))
            ->text($this->textRenderer->render($notification, $this))
            ->html($content);

        // TODO : actually send the email

        try {
            $this->mailer->send($email);
            return new NotificationSenderResult(NotificationStatus::SENT, 'Email sent');
        } catch (TransportExceptionInterface $exception) {
            return new NotificationSenderResult(NotificationStatus::ERROR, 'Email could not be sent ' . $exception->getMessage());
        }
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
