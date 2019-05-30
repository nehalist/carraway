<?php

namespace App\Controller\Admin;

use App\Entity\Notification;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    /**
     * @Route("/", name="dashboard")
     */
    public function index()
    {
        $notifications = $this->getDoctrine()->getRepository(Notification::class)->findBy([], ['id' => 'desc']);

        return $this->render('admin/dashboard.html.twig', [
            'notifications'       => $notifications,
            'mailchimpEnabled'    => ! ! getenv('MAILCHIMP_API_KEY'),
            'saveContactRequests' => ! ! getenv('SAVE_CONTACT_REQUESTS'),
            'dailyCRLimit'        => getenv('DAILY_CONTACT_REQUESTS_LIMIT_PER_IP'),
        ]);
    }
}
