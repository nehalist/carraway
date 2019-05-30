<?php

namespace App\Event;

use App\Entity\Subscription;
use Symfony\Component\EventDispatcher\Event;

class SubscriptionCreatedEvent extends Event
{
    protected $subscription;

    public function __construct(Subscription $subscription)
    {
        $this->subscription = $subscription;
    }

    public function getSubscription()
    {
        return $this->subscription;
    }
}
