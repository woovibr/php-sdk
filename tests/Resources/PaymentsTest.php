<?php

namespace Tests\Resources;

use OpenPix\PhpSdk\Request;
use OpenPix\PhpSdk\RequestTransport;
use OpenPix\PhpSdk\Resources\Payments;
use PHPUnit\Framework\TestCase;

final class PaymentsTest extends TestCase
{
    public function testList(): void
    {
        $requestTransportMock = $this->createMock(RequestTransport::class);

        $payments = new Payments($requestTransportMock);
        $pagedRequest = $payments->list()->getPagedRequest();

        $this->assertSame($pagedRequest->getPath(), "/api/v1/payment");
        $this->assertSame($pagedRequest->getMethod(), "GET");
        $this->assertSame($pagedRequest->getBody(), null);
    }

    public function testGetOne(): void
    {
        $requestTransportMock = $this->createMock(RequestTransport::class);
        $requestTransportMock->expects($this->once())
            ->method("transport")
            ->willReturnCallback(function (Request $request) {
                $this->assertSame("GET", $request->getMethod());
                $this->assertSame("/api/v1/payment/abcd", $request->getPath());
                $this->assertSame($request->getBody(), null);
                $this->assertSame($request->getQueryParams(), []);

                return ["payment" => [], "transaction" => []];
            });

        $payments = new Payments($requestTransportMock);
        $result = $payments->getOne("abcd");

        $this->assertSame($result, ["payment" => [], "transaction" => []]);
    }

    public function testCreate(): void
    {
        $requestBody = [
            "value" => 42,
        ];

        $requestTransportMock = $this->createMock(RequestTransport::class);
        $requestTransportMock->expects($this->once())
            ->method("transport")
            ->willReturnCallback(function (Request $request) use ($requestBody) {
                $this->assertSame("POST", $request->getMethod());
                $this->assertSame("/api/v1/payment", $request->getPath());
                $this->assertSame($request->getBody(), $requestBody);
                $this->assertSame($request->getQueryParams(), []);

                return ["payment" => []];
            });

        $payments = new Payments($requestTransportMock);
        $result = $payments->create($requestBody);

        $this->assertSame(["payment" => []], $result);
    }

    public function testApprove(): void
    {
        $correlationID = '42';

        $requestBody = [
            "correlationID" => $correlationID,
        ];

        $requestTransportMock = $this->createMock(RequestTransport::class);
        $requestTransportMock->expects($this->once())
            ->method("transport")
            ->willReturnCallback(function (Request $request) use ($requestBody) {
                $this->assertSame("POST", $request->getMethod());
                $this->assertSame("/api/v1/payment/approve", $request->getPath());
                $this->assertSame($request->getBody(), $requestBody);
                $this->assertSame($request->getQueryParams(), []);

                return ["payment" => []];
            });

        $payments = new Payments($requestTransportMock);
        $result = $payments->approve($correlationID);

        $this->assertSame(["payment" => []], $result);
    }
}
