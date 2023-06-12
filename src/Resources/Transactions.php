<?php

namespace OpenPix\PhpSdk\Resources;

use OpenPix\PhpSdk\Paginator;
use OpenPix\PhpSdk\Request;
use OpenPix\PhpSdk\RequestTransport;

class Transactions
{
    private RequestTransport $requestTransport;

    public function __construct(RequestTransport $requestTransport)
    {
        $this->requestTransport = $requestTransport;
    }

    /**
     * @return array<string, mixed>
     */
    public function getOne(string $transactionID): array
    {
        $request = (new Request())
            ->method("GET")
            ->path("/transaction/" . $transactionID);

        return $this->requestTransport->transport($request);
    }

    /**
     * @param array<string, mixed> $queryParams
     * @return Paginator
     */
    public function list(array $queryParams): Paginator
    {
        $request = (new Request())
            ->method("GET")
            ->path("/transaction")
            ->queryParams($queryParams);

        return new Paginator($this->requestTransport, $request);
    }
}
