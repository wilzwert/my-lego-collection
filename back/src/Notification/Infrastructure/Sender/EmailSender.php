<?php

namespace App\Notification\Infrastructure\Sender;

use App\Notification\Domain\Model\Notification;
use App\Notification\Domain\Model\NotificationSendResult;
use App\Notification\Domain\Model\NotificationStatus;
use App\Notification\Infrastructure\Renderer\NotificationRenderer;
use App\Notification\Infrastructure\Renderer\NotificationSubjectGenerator;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

/**
 * @author Wilhelm Zwertvaegher
 */
final readonly class EmailSender implements NotificationSender
{

    public function __construct(
        private MailerInterface              $mailer,
        private NotificationRenderer         $renderer,
        private NotificationRenderer         $textRenderer,
        private NotificationSubjectGenerator $subjectGenerator
    )
    {
    }

    public function supports(Notification $notification): bool
    {
        return true;
    }

    public function send(Notification $notification): NotificationSendResult
    {
        $content = $this->renderer->render($notification);

        $email = new Email()
            ->from('hello@example.com')
            ->to($notification->getIdentityInfo()->getEmail())
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject($this->subjectGenerator->generate($notification))
            ->text($this->textRenderer->render($notification))
            ->html($content);

        // TODO : actually send the email

        $this->mailer->send($email);

        return new NotificationSendResult('email', NotificationStatus::SENT, 'Email sent');
    }
}
