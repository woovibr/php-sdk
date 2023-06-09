<?php

namespace OpenPix\PhpSdk\Resources;

use OpenPix\PhpSdk\Request;
use OpenPix\PhpSdk\RequestTransport;

class Transactions
{
    private RequestTransport $requestTransport;

    public function __construct(RequestTransport $requestTransport)
    {
        $this->requestTransport = $requestTransport;
    }

    public function getOne(string $transactionID): array
    {
        $request = (new Request())
            ->method("GET")
            ->path("/transaction/" . $transactionID);

        return $this->requestTransport->transport($request);
    }
}
