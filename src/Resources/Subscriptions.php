<?php

namespace OpenPix\PhpSdk\Resources;

use OpenPix\PhpSdk\Request;
use OpenPix\PhpSdk\RequestTransport;

class Subscriptions
{
    private RequestTransport $requestTransport;

    public function __construct(RequestTransport $requestTransport)
    {
        $this->requestTransport = $requestTransport;
    }

    public function getOne(string $subscriptionID): array
    {
        $request = (new Request())
            ->method("GET")
            ->path("/subscriptions/" . $subscriptionID);

        return $this->requestTransport->transport($request);
    }

    public function create(array $subscription): array
    {
        $request = (new Request())
            ->method("POST")
            ->path("/subscriptions")
            ->body($subscription);

        return $this->requestTransport->transport($request);
    }
}
