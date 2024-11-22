<?php

namespace Tests\Resources;

use DateTimeImmutable;
use OpenPix\PhpSdk\Request;
use OpenPix\PhpSdk\RequestTransport;
use OpenPix\PhpSdk\Resources\Transactions;
use PHPUnit\Framework\TestCase;

final class TransactionsTest extends TestCase
{
    public function testGetOne(): void
    {
        $requestTransportMock = $this->createMock(RequestTransport::class);

        $requestTransportMock->expects($this->once())
            ->method("transport")
            ->willReturnCallback(function (Request $request) {
                $this->assertSame("GET", $request->getMethod());
                $this->assertSame("/api/v1/transaction/transactionId", $request->getPath());
                $this->assertSame($request->getBody(), null);
                $this->assertSame($request->getQueryParams(), []);

                return ["transaction" => []];
            });

        $transactions = new Transactions($requestTransportMock);

        $result = $transactions->getOne("transactionId");

        $this->assertSame($result, ["transaction" => []]);
    }

    public function testList(): void
    {
        $requestTransportMock = $this->createMock(RequestTransport::class);

        $params = [
            "withdrawal" => "withdrawalId",
            "pixQrCode" => "pixQrCodeId",
            "charge" => "chargeId",
            "start" => "startDate",
            "end" => "endDate",
            "skip" => 10,
            "limit" => 50,
        ];

        $transactions = new Transactions($requestTransportMock);
        $pagedRequest = $transactions->list($params)->perPage(50)->skip(10)->getPagedRequest();

        $this->assertSame($pagedRequest->getPath(), "/api/v1/transaction");
        $this->assertSame($pagedRequest->getMethod(), "GET");
        $this->assertSame($pagedRequest->getBody(), null);
        $this->assertSame($pagedRequest->getQueryParams(), $params);
    }
}
