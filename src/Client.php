<?php

namespace OpenPix\PhpSdk;

use OpenPix\PhpSdk\RequestTransport;
use OpenPix\PhpSdk\Resources\Charges;
use OpenPix\PhpSdk\Resources\Customers;

class Client
{
    private RequestTransport $requestTransport;

    public static function create(string $appId)
    {
        return new Client(new RequestTransport($appId));
    }

    public function __construct(RequestTransport $requestTransport)
    {
        $this->requestTransport = $requestTransport;
    }

    public function getRequestTransport(): RequestTransport
    {
        return $this->requestTransport;
    }

    public function customers(): Customers
    {
        return new Customers($this->requestTransport);
    }

    public function charges(): Charges
    {
        return new Charges($this->requestTransport);
    }
}
