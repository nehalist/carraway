<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationListInterface;

abstract class AbstractApiController extends AbstractController
{
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
}
