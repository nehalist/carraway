<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

class CoreExceptionListener
{
    public function onCoreException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();

        $controller = $event->getRequest()->attributes->get('_controller');
        if (strstr($controller, 'Controller\Api') > -1) {
            $event->setResponse(new JsonResponse([
                'error'   => get_class($exception),
                'message' => $exception->getMessage(),
            ], Response::HTTP_BAD_REQUEST));
        }
    }
}
