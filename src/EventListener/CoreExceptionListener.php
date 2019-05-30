<?php

namespace App\EventListener;

use App\Response\ErrorResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

class CoreExceptionListener
{
    public function onCoreException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();
        $env       = getenv('APP_ENV');

        if (! $env) {
            $env = 'prod';
        }

        $controller = $event->getRequest()->attributes->get('_controller');
        if (strstr($controller, 'Controller\Api') > -1) {
            if (in_array($env, ['dev', 'development'])) {
                return $event->setResponse(new ErrorResponse([
                    get_class($exception) . ': ' . $exception->getMessage()
                ]));
            }

            return $event->setResponse(new ErrorResponse('System Error'));
        }

        return null;
    }
}
