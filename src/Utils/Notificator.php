<?php

namespace App\Utils;

use App\Entity\Notification;
use App\Event\NotificationCreatedEvent;
use App\Events;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class Notificator
{
    protected $em;

    protected $eventDispatcher;

    public function __construct(EntityManagerInterface $em, EventDispatcherInterface $eventDispatcher)
    {
        $this->em              = $em;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function success($title, $message)
    {
        return $this->create($title, $message, 'success');
    }

    public function error($title, $message)
    {
        return $this->create($title, $message, 'danger');
    }

    public function create($title, $message, $type)
    {
        $notification = new Notification();
        $notification->setCreatedAt(new DateTime());
        $notification->setTitle($title);
        $notification->setMessage($message);
        $notification->setType($type);

        $this->em->persist($notification);
        $this->em->flush();

        $this->eventDispatcher->dispatch(Events::NOTIFICATION_CREATED, new NotificationCreatedEvent($notification));

        return $notification;
    }
}
