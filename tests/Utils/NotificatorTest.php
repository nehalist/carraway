<?php

namespace App\Tests\Utils;

use App\Entity\Notification;
use App\Event\NotificationCreatedEvent;
use App\Events;
use App\Utils\Notificator;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class NotificatorTest extends TestCase
{
    public function testSuccess()
    {
        $em              = $this->createMock(EntityManagerInterface::class);
        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);

        $notificator = new Notificator($em, $eventDispatcher);

        $notification = $notificator->success('the success', 'the success message');

        $this->assertEquals('success', $notification->getType());
        $this->assertEquals('the success message', $notification->getMessage());
        $this->assertEquals('the success', $notification->getTitle());
    }

    public function testError()
    {
        $em              = $this->createMock(EntityManagerInterface::class);
        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);

        $notificator = new Notificator($em, $eventDispatcher);

        $notification = $notificator->error('the error', 'the error message');

        $this->assertEquals('danger', $notification->getType());
        $this->assertEquals('the error message', $notification->getMessage());
        $this->assertEquals('the error', $notification->getTitle());
    }

    public function testCreate()
    {
        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects($this->once())
           ->method('persist')
           ->with($this->isInstanceOf(Notification::class));
        $em->expects($this->once())
           ->method('flush');

        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $eventDispatcher->expects($this->once())
                        ->method('dispatch')
                        ->with(Events::NOTIFICATION_CREATED, $this->isInstanceOf(NotificationCreatedEvent::class));

        $notificator = new Notificator($em, $eventDispatcher);

        $notification = $notificator->create('the title', 'the message', 'the type');

        $this->assertInstanceOf(Notification::class, $notification);
        $this->assertEquals('the title', $notification->getTitle());
        $this->assertEquals('the message', $notification->getMessage());
        $this->assertEquals('the type', $notification->getType());
        $this->assertInstanceOf(\DateTimeInterface::class, $notification->getCreatedAt());
    }
}
