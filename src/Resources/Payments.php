<?php

namespace OpenPix\PhpSdk\Resources;

use OpenPix\PhpSdk\Paginator;
use OpenPix\PhpSdk\Request;
use OpenPix\PhpSdk\RequestTransport;

class Payments
{
    private RequestTransport $requestTransport;

    public function __construct(RequestTransport $requestTransport)
    {
        $this->requestTransport = $requestTransport;
    }

    public function list(array $params = []): Paginator
    {
        $request = (new Request())
            ->method("GET")
            ->path("/payment")
            ->queryParams($params);

        return new Paginator($this->requestTransport, $request);
    }

    public function getOne(string $paymentID): array
    {
        $request = (new Request())
            ->method("GET")
            ->path("/payment/" . $paymentID);

        return $this->requestTransport->transport($request);
    }

    public function create(array $payment): array
    {
        $request = (new Request())
            ->method("POST")
            ->path("/payment")
            ->body($payment);

        return $this->requestTransport->transport($request);
    }
}
