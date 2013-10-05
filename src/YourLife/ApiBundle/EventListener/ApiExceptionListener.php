<?php

namespace YourLife\ApiBundle\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use YourLife\ApiBundle\Exception\ApiException;
use YourLife\ApiBundle\Exception\ApiExceptionDetail;

class ApiExceptionListener
{
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        /** @var ApiException $exception */
        $exception = $event->getException();
        if ($exception instanceof ApiException) {

            $responseData = [
                'error' => [
                    'type'      => $exception->getType(),
                    'message'   => $exception->getMessage(),
                    'details'   => array_map(function(ApiExceptionDetail $detail) {
                        return $detail->toArray();
                    }, $exception->getDetails())
                ]
            ];

            $response = new JsonResponse($responseData);
            $event->setResponse($response);
        }
    }
} 