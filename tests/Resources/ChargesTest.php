<?php

namespace Tests\Resources;

use OpenPix\PhpSdk\Request;
use OpenPix\PhpSdk\RequestTransport;
use OpenPix\PhpSdk\Resources\Charges;
use PHPUnit\Framework\TestCase;

final class ChargesTest extends TestCase
{
    public function testGetOne(): void
    {
        $charge = [["charge" => ["value" => 50]]];

        $requestTransportMock = $this->createMock(RequestTransport::class);
        $requestTransportMock->expects($this->once())
            ->method("transport")
            ->willReturnCallback(function (Request $request) use ($charge) {
                $this->assertSame("GET", $request->getMethod());
                $this->assertSame("/api/v1/charge/abcd", $request->getPath());
                $this->assertSame($request->getBody(), null);
                $this->assertSame($request->getQueryParams(), []);

                return $charge;
            });

        $charges = new Charges($requestTransportMock);
        $result = $charges->getOne("abcd");

        $this->assertSame($result, $charge);
    }

    public function testCreate(): void
    {
        $requestBody = [
            "correlationID" => "abcd",
            "value" => 100,
        ];

        $requestTransportMock = $this->createMock(RequestTransport::class);
        $requestTransportMock->expects($this->once())
            ->method("transport")
            ->willReturnCallback(function (Request $request) use ($requestBody) {
                $this->assertSame("POST", $request->getMethod());
                $this->assertSame("/api/v1/charge", $request->getPath());
                $this->assertSame($request->getBody(), $requestBody);
                $this->assertSame($request->getQueryParams(), [
                    "return_existing" => true,
                ]);

                return ["correlationID" => "abcd"];
            });

        $charges = new Charges($requestTransportMock);
        $result = $charges->create($requestBody, true);

        $this->assertSame(["correlationID" => "abcd"], $result);
    }

    public function testList(): void
    {
        $requestTransportMock = $this->createMock(RequestTransport::class);

        $charges = new Charges($requestTransportMock);
        $pagedRequest = $charges->list()->getPagedRequest();

        $this->assertSame($pagedRequest->getPath(), "/api/v1/charge");
        $this->assertSame($pagedRequest->getMethod(), "GET");
        $this->assertSame($pagedRequest->getBody(), null);
    }

    public function testDelete(): void
    {
        $requestTransportMock = $this->createMock(RequestTransport::class);
        $requestTransportMock->expects($this->once())
            ->method("transport")
            ->willReturnCallback(function (Request $request) {
                $this->assertSame("DELETE", $request->getMethod());
                $this->assertSame("/api/v1/charge/abcd", $request->getPath());
                $this->assertSame($request->getBody(), null);
                $this->assertSame($request->getQueryParams(), []);

                return ["id" => "abcd"];
            });

        $charges = new Charges($requestTransportMock);
        $result = $charges->delete("abcd");

        $this->assertSame($result, ["id" => "abcd"]);
    }

    public function testGetQrCodeImageLink(): void
    {
        $charges = new Charges($this->createConfiguredMock(RequestTransport::class, [
            "getBaseUri" => "https://example.com",
        ]));

        $result = $charges->getQrCodeImageLink("123456", 256);

        $this->assertSame("https://example.com/openpix/charge/brcode/image/123456.png?size=256", $result);
    }
}
