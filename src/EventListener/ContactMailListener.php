<?php

namespace App\EventListener;

use App\Event\ContactRequestEvent;
use App\Utils\Notificator;
use Exception;
use Swift_Mailer;
use Swift_Message;

class ContactMailListener
{
    protected $mailer;

    protected $notificator;

    public function __construct(Swift_Mailer $mailer, Notificator $notificator)
    {
        $this->mailer      = $mailer;
        $this->notificator = $notificator;
    }

    public function onContactRequest(ContactRequestEvent $event)
    {
        $contactRequest = $event->getContactRequestEntity();

        $from = getenv('CONTACT_REQUEST_FROM');
        $to   = getenv('CONTACT_REQUEST_TO');

        if (! $to) {
            return;
        }

        $message = (new Swift_Message())
            ->setFrom($from)
            ->setReplyTo($contactRequest->getMail(), $contactRequest->getName())
            ->setTo($to)
            ->setSubject($contactRequest->getSubject())
            ->setBody($contactRequest->getMessage());

        try {
            $this->mailer->send($message);
        } catch (Exception $exception) {
            $this->notificator->error('Failed to send mail', $exception->getMessage());
        }
    }
}
