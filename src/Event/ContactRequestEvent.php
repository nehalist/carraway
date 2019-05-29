<?php

namespace App\Event;

use App\Entity\ContactRequest;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;

class ContactRequestEvent extends Event
{
    protected $request;

    protected $contactRequestEntity;

    public function __construct(Request $request, ContactRequest $contactRequestEntity)
    {
        $this->request              = $request;
        $this->contactRequestEntity = $contactRequestEntity;
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function getContactRequestEntity()
    {
        return $this->contactRequestEntity;
    }
}
