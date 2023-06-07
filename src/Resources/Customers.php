<?php

namespace OpenPix\PhpSdk\Resources;

use OpenPix\PhpSdk\Paginator;
use OpenPix\PhpSdk\Request;
use OpenPix\PhpSdk\RequestTransport;

class Customers
{
    private RequestTransport $requestTransport;

    public function __construct(RequestTransport $requestTransport)
    {
        $this->requestTransport = $requestTransport;
    }

    public function list(): Paginator
    {
        $request = (new Request())
            ->method("GET")
            ->path("/customer");

        return new Paginator($this->requestTransport, $request);
    }
}
