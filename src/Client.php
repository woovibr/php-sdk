<?php

namespace OpenPix\PhpSdk;

use OpenPix\PhpSdk\RequestTransport;
use OpenPix\PhpSdk\Resources\Charges;
use OpenPix\PhpSdk\Resources\Customers;

class Client
{
    public const BASE_URI = "https://api.openpix.com.br/api/v1";

    private RequestTransport $requestTransport;

    public static function create(string $appId, string $baseUri = self::BASE_URI)
    {
        return new Client(new RequestTransport($appId, $baseUri));
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
