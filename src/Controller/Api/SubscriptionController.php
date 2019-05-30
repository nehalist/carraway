<?php

namespace App\Controller\Api;

use App\Entity\Subscription;
use App\Event\SubscriptionCreatedEvent;
use App\Events;
use App\Response\ErrorResponse;
use App\Utils\Notificator;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validation;

class SubscriptionController extends AbstractApiController
{
    /**
     * @Route("/subscription", name="subscriptionCreate", methods={"POST"})
     *
     * @param Request                  $request
     * @param EventDispatcherInterface $eventDispatcher
     * @param EntityManagerInterface   $em
     * @param Notificator              $notificator
     *
     * @return JsonResponse
     * @throws \Exception
     */
    public function create(
        Request $request,
        EventDispatcherInterface $eventDispatcher,
        EntityManagerInterface $em,
        Notificator $notificator
    ) {
        $payload     = $this->getPayload();
        $validator   = Validation::createValidator();
        $constraints = new Assert\Collection([
            'name' => new Assert\Optional(),
            'mail' => new Assert\Email(),
        ]);

        $violations = $validator->validate($payload, $constraints);
        if ($violations->count() > 0) {
            return $this->error($violations);
        }

        $name = isset($payload['name']) ? $payload['name'] : '';
        $mail = $payload['mail'];

        if ($em->getRepository(Subscription::class)->findBy(['mail' => $mail])) {
            return new ErrorResponse('Email already subscribed');
        }

        $subscription = new Subscription();
        $subscription->setName($name);
        $subscription->setMail($mail);
        $subscription->setCreatedAt(new DateTime());
        $subscription->setIp($request->getClientIp());

        $em->persist($subscription);
        $em->flush();

        $notificator->success('New subscription', "From $mail ($name)");

        $eventDispatcher->dispatch(Events::SUBSCRIPTION_CREATED, new SubscriptionCreatedEvent($subscription));

        return $this->json($subscription->toArray());
    }
}
