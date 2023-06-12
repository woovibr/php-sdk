<?php

namespace Tests;

use OpenPix\PhpSdk\RequestTransport;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;

final class RequestTransportTest extends TestCase
{
    public function testTransport(): void
    {
        $expectedResult = ["data" => "hello, world."];
        $streamMock = $this->createConfiguredMock(StreamInterface::class, [
            "getContents" => json_encode($expectedResult),
        ]);
        $responseMock = $this->createConfiguredMock(ResponseInterface::class, [
            "getBody" => $streamMock,
        ]);

        $requestMock = $this->createMock(RequestInterface::class);
        $requestMock->expects($this->exactly(2))
            ->method("withAddedHeader")
            ->willReturnCallback(function ($header, $value) use ($requestMock) {
                if ($header === "Authorization") {
                    $this->assertSame($value, "appId");
                } elseif ($header !== "User-Agent") {
                    $this->assertSame($header, "User-Agent");
                }

                return $requestMock;
            });

        $httpClientMock = $this->createMock(ClientInterface::class);
        $httpClientMock->expects($this->once())
            ->method("sendRequest")
            ->with($requestMock)
            ->willReturn($responseMock);

        $requestTransport = new RequestTransport(
            "appId",
            "https://example.com",
            $httpClientMock,
            $this->createMock(RequestFactoryInterface::class),
            $this->createMock(StreamFactoryInterface::class),
        );

        $this->assertSame($requestTransport->transport($requestMock), $expectedResult);
    }
}
