<?php

namespace OpenPix\PhpSdk;

use Exception;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class ApiErrorException extends Exception
{
    private RequestInterface $request;
    private ResponseInterface $response;

    public static function from(string $errorMessage, RequestInterface $request, ResponseInterface $response)
    {
        $exception = new self($errorMessage);

        $exception->request = $request;
        $exception->response = $response;

        return $exception;
    }

    public function getHttpRequest(): RequestInterface
    {
        return $this->request;
    }

    public function getHttpResponse(): ResponseInterface
    {
        return $this->response;
    }
}
