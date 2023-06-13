<?php

namespace OpenPix\PhpSdk\Resources;

use OpenPix\PhpSdk\Paginator;
use OpenPix\PhpSdk\Request;
use OpenPix\PhpSdk\RequestTransport;

class Refunds
{
    private RequestTransport $requestTransport;

    public function __construct(RequestTransport $requestTransport)
    {
        $this->requestTransport = $requestTransport;
    }

    /**
     * @param array<mixed> $params
     */
    public function list(array $params = []): Paginator
    {
        $request = (new Request())
            ->method("GET")
            ->path("/refund")
            ->queryParams($params);

        return new Paginator($this->requestTransport, $request);
    }

    /**
     * @return array<mixed>
     */
    public function getOne(string $refundID): array
    {
        $request = (new Request())
            ->method("GET")
            ->path("/refund/" . $refundID);

        return $this->requestTransport->transport($request);
    }

    /**
     * @param array<mixed> $refund
     * @return array<mixed>
     */
    public function create(array $refund): array
    {
        $request = (new Request())
            ->method("POST")
            ->path("/refund")
            ->body($refund);

        return $this->requestTransport->transport($request);
    }
}
