<?php

namespace App\Controller\Api;

use App\Response\ErrorResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
            $errors[] = $violation->getPropertyPath() . ': ' .$violation->getMessage();
        }

        return new ErrorResponse([$errors]);
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
