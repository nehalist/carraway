<?php

namespace App\Tests\EventListener;

use App\Entity\ContactRequest;
use App\Event\ContactRequestEvent;
use App\EventListener\ContactMailListener;
use App\Utils\Notificator;
use Exception;
use PHPUnit\Framework\TestCase;
use Swift_Mailer;

class ContactMailListenerTest extends TestCase
{
    public function testOnContactRequest()
    {
        $mailer = $this->createMock(Swift_Mailer::class);
        $mailer->expects($this->once())
               ->method('send');

        $notificator = $this->createMock(Notificator::class);

        $listener = new ContactMailListener($mailer, $notificator);

        $contactRequest = $this->createMock(ContactRequest::class);

        $event = $this->createMock(ContactRequestEvent::class);
        $event->method('getContactRequestEntity')
              ->willReturn($contactRequest);

        $listener->onContactRequest($event);
    }

    public function testOnContactRequestsException()
    {
        $mailer = $this->createMock(Swift_Mailer::class);
        $mailer->expects($this->once())
               ->method('send')
               ->willThrowException($this->createMock(Exception::class));

        $notificator = $this->createMock(Notificator::class);
        $notificator->expects($this->once())
                    ->method('error');

        $listener = new ContactMailListener($mailer, $notificator);

        $contactRequest = $this->createMock(ContactRequest::class);

        $event = $this->createMock(ContactRequestEvent::class);
        $event->method('getContactRequestEntity')
              ->willReturn($contactRequest);

        $listener->onContactRequest($event);
    }
}
