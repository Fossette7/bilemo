<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ExceptionSubscriber implements EventSubscriberInterface
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

      //catch exception and display on Json instead of Http
      if ($exception instanceof \HttpException) {
        $data = [
          'status' => $exception->getStatusCode(),
          'message' => $exception->getMessage()
        ];

        $event->setResponse(new JsonResponse($data));

      } else {
        $data = [
          'message' => $exception->getMessage()
        ];

        $event->setResponse(new JsonResponse($data));
      }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }
}
