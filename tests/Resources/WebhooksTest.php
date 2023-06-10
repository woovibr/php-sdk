<?php

namespace Tests;

use OpenPix\PhpSdk\Request;
use OpenPix\PhpSdk\RequestTransport;
use OpenPix\PhpSdk\Resources\Webhooks;
use PHPUnit\Framework\TestCase;

final class WebhooksTest extends TestCase
{
    public function testList(): void
    {
        $requestTransportMock = $this->createMock(RequestTransport::class);

        $webhooks = new Webhooks($requestTransportMock);
        $pagedRequest = $webhooks->list("https://example.com")
            ->perPage(15)
            ->skip(42)
            ->getPagedRequest();

        $this->assertSame($pagedRequest->getPath(), "/webhook");
        $this->assertSame($pagedRequest->getMethod(), "GET");
        $this->assertSame($pagedRequest->getBody(), null);
        $this->assertSame($pagedRequest->getQueryParams(), [
            "url" => "https://example.com",
            "skip" => 42,
            "limit" => 15,
        ]);
    }

    public function testDelete(): void
    {
        $requestTransportMock = $this->createMock(RequestTransport::class);
        $requestTransportMock->expects($this->once())
            ->method("transport")
            ->willReturnCallback(function (Request $request) {
                $this->assertSame("DELETE", $request->getMethod());
                $this->assertSame("/webhook/abcd", $request->getPath());
                $this->assertSame($request->getBody(), null);
                $this->assertSame($request->getQueryParams(), []);

                return ["status" => ""];
            });

        $webhooks = new Webhooks($requestTransportMock);
        $result = $webhooks->delete("abcd");

        $this->assertSame($result, ["status" => ""]);
    }

    public function testCreate(): void
    {
        $requestBody = [
            "webhook" => [],
        ];

        $requestTransportMock = $this->createMock(RequestTransport::class);
        $requestTransportMock->expects($this->once())
            ->method("transport")
            ->willReturnCallback(function (Request $request) use ($requestBody) {
                $this->assertSame("POST", $request->getMethod());
                $this->assertSame("/webhook", $request->getPath());
                $this->assertSame($request->getBody(), $requestBody);
                $this->assertSame($request->getQueryParams(), []);

                return ["webhook" => []];
            });

        $webhooks = new Webhooks($requestTransportMock);
        $result = $webhooks->create($requestBody);

        $this->assertSame(["webhook" => []], $result);
    }
}
