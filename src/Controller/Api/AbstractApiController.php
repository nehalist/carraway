<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationListInterface;

abstract class AbstractApiController extends AbstractController
{
    protected $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    protected function error(ConstraintViolationListInterface $violations)
    {
        $errors = [];

        foreach ($violations as $violation) {
            /** @var ConstraintViolation $violation */
            $errors[] = [
                'propertyPath' => $violation->getPropertyPath(),
                'message'      => $violation->getMessage(),
            ];
        }

        return new JsonResponse(['errors' => $errors], Response::HTTP_BAD_REQUEST);
    }

    protected function getPayload()
    {
        $payload = $this->requestStack->getCurrentRequest()->getContent();
        if (! $payload || ! ($payload = json_decode($payload, true))) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, 'Invalid request');
        }
        return $payload;
    }
}
