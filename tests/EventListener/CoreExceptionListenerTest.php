<?php

namespace App\Tests\EventListener;

use App\EventListener\CoreExceptionListener;
use Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

class CoreExceptionListenerTest extends TestCase
{
    public function testOnCoreExceptionOnNonApiRoutes()
    {
        $exception    = $this->createMock(Exception::class);
        $parameterBag = $this->createMock(ParameterBag::class);
        $parameterBag->method('get')
                     ->with('_controller')
                     ->willReturn('Controller\Admin');

        $request             = $this->createMock(Request::class);
        $request->attributes = $parameterBag;

        $event = $this->createMock(GetResponseForExceptionEvent::class);
        $event->method('getException')->willReturn($exception);
        $event->method('getRequest')->willReturn($request);

        $event->expects($this->never())->method('setResponse');

        $listener = new CoreExceptionListener();
        $listener->onCoreException($event);
    }

    public function testOnCoreException()
    {
        $exception    = $this->createMock(Exception::class);
        $parameterBag = $this->createMock(ParameterBag::class);
        $parameterBag->method('get')
                     ->with('_controller')
                     ->willReturn('Controller\Api');

        $request             = $this->createMock(Request::class);
        $request->attributes = $parameterBag;

        $event = $this->createMock(GetResponseForExceptionEvent::class);
        $event->method('getException')->willReturn($exception);
        $event->method('getRequest')->willReturn($request);

        $event->expects($this->once())->method('setResponse');

        $listener = new CoreExceptionListener();
        $listener->onCoreException($event);
    }
}
