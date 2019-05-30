<?php

namespace App\Controller\Admin;

use App\Entity\Subscription;
use App\Event\SubscriptionDeletedEvent;
use App\Events;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

class SubscriptionController extends AbstractController
{
    /**
     * @Route("/subscriptions", name="subscriptionOverview")
     */
    public function index()
    {
        $subscriptions = $this->getDoctrine()->getRepository(Subscription::class)->findBy([], ['id' => 'desc']);

        return $this->render('admin/subscriptions/overview.html.twig', [
            'subscriptions' => $subscriptions,
        ]);
    }

    /**
     * @Route("/subscription/{subscription}/delete", name="subscriptionDelete")
     *
     * @param Subscription             $subscription
     * @param EventDispatcherInterface $eventDispatcher
     *
     * @return RedirectResponse
     */
    public function delete(Subscription $subscription, EventDispatcherInterface $eventDispatcher)
    {
        $this->getDoctrine()->getManager()->remove($subscription);
        $this->getDoctrine()->getManager()->flush();

        $this->addFlash('success', 'Subscription deleted');

        $eventDispatcher->dispatch(Events::SUBSCRIPTION_DELETED, new SubscriptionDeletedEvent($subscription));

        return $this->redirectToRoute('subscriptionOverview');
    }
}
