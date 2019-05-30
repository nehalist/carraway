<?php

namespace App\EventListener;

use App\Event\ContactRequestEvent;
use Swift_Mailer;
use Swift_Message;

class ContactMailListener
{
    protected $mailer;

    public function __construct(Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
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
            ->setTo($to)
            ->setSubject($contactRequest->getSubject())
            ->setBody($contactRequest->getMessage())
        ;

        $this->mailer->send($message);
    }
}
