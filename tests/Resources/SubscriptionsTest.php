<?php

namespace Tests;

use OpenPix\PhpSdk\Request;
use OpenPix\PhpSdk\RequestTransport;
use OpenPix\PhpSdk\Resources\Subscriptions;
use PHPUnit\Framework\TestCase;

final class SubscriptionsTest extends TestCase
{
    public function testGetOne(): void
    {
        $requestTransportMock = $this->createMock(RequestTransport::class);
        $requestTransportMock->expects($this->once())
            ->method("transport")
            ->willReturnCallback(function (Request $request) {
                $this->assertSame("GET", $request->getMethod());
                $this->assertSame("/api/v1/subscriptions/abcd", $request->getPath());
                $this->assertSame($request->getBody(), null);
                $this->assertSame($request->getQueryParams(), []);

                return ["subscription" => []];
            });

        $subscriptions = new Subscriptions($requestTransportMock);
        $result = $subscriptions->getOne("abcd");

        $this->assertSame($result, ["subscription" => []]);
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
                $this->assertSame("/api/v1/subscriptions", $request->getPath());
                $this->assertSame($request->getBody(), $requestBody);
                $this->assertSame($request->getQueryParams(), []);

                return ["subscription" => []];
            });

        $subscriptions = new Subscriptions($requestTransportMock);
        $result = $subscriptions->create($requestBody);

        $this->assertSame(["subscription" => []], $result);
    }
}
