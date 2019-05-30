<?php

namespace App\Controller\Admin;

use App\Entity\ContactRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactRequestController extends AbstractController
{
    /**
     * @Route("/contact-requests", name="contactRequestsOverview")
     */
    public function index()
    {
        $contactRequests = $this->getDoctrine()->getRepository(ContactRequest::class)->findBy([], ['id' => 'desc']);

        return $this->render('admin/contact-requests/overview.html.twig', [
            'contactRequests' => $contactRequests,
        ]);
    }

    /**
     * @Route("/contact-requests/{contactRequest}", name="contactRequestDetails")
     *
     * @param ContactRequest $contactRequest
     *
     * @return Response
     */
    public function details(ContactRequest $contactRequest)
    {
        return $this->render('admin/contact-requests/details.html.twig', [
            'contactRequest' => $contactRequest,
        ]);
    }

    /**
     * @Route("/contact-requests/{contactRequest}/delete", name="contactRequestDelete")
     *
     * @param ContactRequest $contactRequest
     *
     * @return RedirectResponse
     */
    public function delete(ContactRequest $contactRequest)
    {
        $this->getDoctrine()->getManager()->remove($contactRequest);
        $this->getDoctrine()->getManager()->flush();

        $this->addFlash('success', 'Successfully deleted');

        return $this->redirectToRoute('contactRequestsOverview');
    }
}
