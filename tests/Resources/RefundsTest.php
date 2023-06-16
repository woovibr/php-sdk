<?php

namespace Tests;

use OpenPix\PhpSdk\Request;
use OpenPix\PhpSdk\RequestTransport;
use OpenPix\PhpSdk\Resources\Refunds;
use PHPUnit\Framework\TestCase;

final class RefundsTest extends TestCase
{
    public function testList(): void
    {
        $requestTransportMock = $this->createMock(RequestTransport::class);

        $refunds = new Refunds($requestTransportMock);
        $pagedRequest = $refunds->list()->getPagedRequest();

        $this->assertSame($pagedRequest->getPath(), "/api/v1/refund");
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
                $this->assertSame("/api/v1/refund/abcd", $request->getPath());
                $this->assertSame($request->getBody(), null);
                $this->assertSame($request->getQueryParams(), []);

                return ["refund" => []];
            });

        $refunds = new Refunds($requestTransportMock);
        $result = $refunds->getOne("abcd");

        $this->assertSame($result, ["refund" => []]);
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
                $this->assertSame("/api/v1/refund", $request->getPath());
                $this->assertSame($request->getBody(), $requestBody);
                $this->assertSame($request->getQueryParams(), []);

                return ["refund" => []];
            });

        $refunds = new Refunds($requestTransportMock);
        $result = $refunds->create($requestBody);

        $this->assertSame(["refund" => []], $result);
    }
}
