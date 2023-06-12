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

    /**
     * @param array<mixed> $params
     * @return Paginator
     */
    public function list(array $params = []): Paginator
    {
        $request = (new Request())
            ->method("GET")
            ->path("/payment")
            ->queryParams($params);

        return new Paginator($this->requestTransport, $request);
    }

    /**
     * @return array<mixed>
     */
    public function getOne(string $paymentID): array
    {
        $request = (new Request())
            ->method("GET")
            ->path("/payment/" . $paymentID);

        return $this->requestTransport->transport($request);
    }

    /**
     * @param array<mixed> $payment
     * @return array<mixed>
     */
    public function create(array $payment): array
    {
        $request = (new Request())
            ->method("POST")
            ->path("/payment")
            ->body($payment);

        return $this->requestTransport->transport($request);
    }
}
