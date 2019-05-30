<?php

namespace App\Event;

use App\Entity\Notification;
use Symfony\Component\EventDispatcher\Event;

class NotificationCreatedEvent extends Event
{
    protected $notification;

    public function __construct(Notification $notification)
    {
        $this->notification = $notification;
    }

    public function getNotification()
    {
        return $this->notification;
    }
}
