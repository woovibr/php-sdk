<?php

namespace OpenPix\PhpSdk\Resources;

use OpenPix\PhpSdk\Paginator;
use OpenPix\PhpSdk\Request;
use OpenPix\PhpSdk\RequestTransport;

class Webhooks
{
    private RequestTransport $requestTransport;

    public function __construct(RequestTransport $requestTransport)
    {
        $this->requestTransport = $requestTransport;
    }

    public function list(string $filterByUrl = ""): Paginator
    {
        $request = (new Request())
            ->method("GET")
            ->path("/webhook")
            ->queryParams(["url" => $filterByUrl]);

        return new Paginator($this->requestTransport, $request);
    }

    public function delete(string $webhookID): array
    {
        $request = (new Request())
            ->method("DELETE")
            ->path("/webhook/" . $webhookID);

        return $this->requestTransport->transport($request);
    }

    public function create(array $webhook): array
    {
        $request = (new Request())
            ->method("POST")
            ->path("/webhook")
            ->body($webhook);

        return $this->requestTransport->transport($request);
    }
}
