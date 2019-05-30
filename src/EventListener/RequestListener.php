<?php

namespace App\EventListener;

use App\Entity\ContactRequest;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class RequestListener
{
    protected $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        if ($event->getRequestType() !== HttpKernelInterface::MASTER_REQUEST) {
            return;
        }

        $limit    = (int) getenv('DAILY_CONTACT_REQUESTS_LIMIT_PER_IP');
        $clientIp = $event->getRequest()->getClientIp();

        $contactRequestsByIp = $this->em->getRepository(ContactRequest::class)
                                        ->countByIpForDate($clientIp, new DateTime());

        var_dump($contactRequestsByIp);

        if ($contactRequestsByIp > $limit) {
            $event->setResponse(new JsonResponse([
                'error' => 'Max requests per day exceeded',
            ], Response::HTTP_TOO_MANY_REQUESTS));
        }
    }
}
