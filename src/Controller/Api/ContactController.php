<?php

namespace App\Controller\Api;

use App\Entity\ContactRequest;
use App\Event\ContactRequestEvent;
use App\Events;
use App\Utils\Notificator;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validation;

class ContactController extends AbstractApiController
{
    /**
     * @Route("/contact", name="contactRequest", methods={"POST"})
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
        $validator   = Validation::createValidator();
        $constraints = new Assert\Collection([
            'name'    => new Assert\NotBlank(),
            'mail'    => new Assert\Email(),
            'message' => new Assert\NotBlank(),
        ]);

        $violations = $validator->validate($request->request->all(), $constraints);
        if ($violations->count() > 0) {
            return $this->error($violations);
        }

        $name    = $request->get('name');
        $mail    = $request->get('mail');
        $subject = $request->get('subject', 'Contact Form Submission');
        $message = $request->get('message');

        $contactRequest = new ContactRequest();
        $contactRequest->setName($name);
        $contactRequest->setMail($mail);
        $contactRequest->setSubject($subject);
        $contactRequest->setMessage($message);
        $contactRequest->setCreatedAt(new DateTime());
        $contactRequest->setIp($request->getClientIp());

        if (getenv('SAVE_CONTACT_REQUESTS')) {
            $em->persist($contactRequest);
            $em->flush();
        }

        $notificator->success('Contact requested', "By $mail ($name)");

        $eventDispatcher->dispatch(
            Events::CONTACT_REQUESTED_CREATED,
            new ContactRequestEvent($request, $contactRequest)
        );

        return $this->json($contactRequest->toArray());
    }
}
