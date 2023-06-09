<?php

namespace OpenPix\PhpSdk\Resources;

use OpenPix\PhpSdk\Paginator;
use OpenPix\PhpSdk\Request;
use OpenPix\PhpSdk\RequestTransport;

class Charges
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
            ->path("/charge")
            ->queryParams($params);

        return new Paginator($this->requestTransport, $request);
    }

    public function getOne(string $id): array
    {
        $request = (new Request())
            ->method("GET")
            ->path("/charge/" . $id);

        return $this->requestTransport->transport($request);
    }

    public function create(array $charge, bool $returnExisting = true): array
    {
        $request = (new Request())
            ->method("POST")
            ->path("/charge")
            ->body($charge)
            ->queryParams(["return_existing" => $returnExisting]);

        return $this->requestTransport->transport($request);
    }

    public function delete(string $id): array
    {
        $request = (new Request())
            ->method("DELETE")
            ->path("/charge/" . $id);

        return $this->requestTransport->transport($request);
    }

    public function getQrCodeImageLink(string $paymentLinkId, int $size = 768): string
    {
        return "https://api.woovi.com/openpix/charge/brcode/image/" . $paymentLinkId . ".png?size=" . $size;
    }
}
