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

    /**
     * @return Paginator
     */
    public function list(): Paginator
    {
        $request = (new Request())
            ->method("GET")
            ->path("/customer");

        return new Paginator($this->requestTransport, $request);
    }

    /**
     * @return array<mixed>
     */
    public function getOne(string $correlationID): array
    {
        $request = (new Request())
            ->method("GET")
            ->path("/customer/" . $correlationID);

        return $this->requestTransport->transport($request);
    }

    /**
     * @param array<mixed> $data
     * @return array<mixed>
     */
    public function create(array $data): array
    {
        $request = (new Request())
            ->method("POST")
            ->path("/customer")
            ->body($data);

        return $this->requestTransport->transport($request);
    }
}
