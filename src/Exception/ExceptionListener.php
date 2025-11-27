<?php

namespace App\Exception;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

/**
 * Class ExceptionListener
 *
 * Listener que intercepta excepciones lanzadas en la aplicación Symfony
 * y devuelve una respuesta JSON con el mensaje de error y el código HTTP
 * correspondiente.
 * 
 * @author Arely Dominguez
 * @date 25-11-2025
 */
class ExceptionListener
{
    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();

        $statusCode = $exception instanceof HttpExceptionInterface
            ? $exception->getStatusCode()
            : 500;

        $response = new JsonResponse([
            'error' => $exception->getMessage(),
        ], $statusCode);

        $event->setResponse($response);
    }
}