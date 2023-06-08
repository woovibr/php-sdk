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
        $appId = "app id";

        $requestMock = $this->createStub(RequestInterface::class);
        $responseMock = $this->createMock(ResponseInterface::class);
        $streamMock = $this->createMock(StreamInterface::class);

        $httpClientMock = $this->createMock(ClientInterface::class);
        $requestFactoryMock = $this->createMock(RequestFactoryInterface::class);
        $streamFactoryMock = $this->createMock(StreamFactoryInterface::class);

        $requestMock->expects($this->exactly(2))
            ->method("withAddedHeader")
            ->willReturnCallback(function ($header, $value) use ($requestMock, $appId) {
                if ($header === "Authorization") {
                    $this->assertSame($value, $appId);
                } elseif ($header !== "User-Agent") {
                    $this->assertSame($header, "User-Agent");
                }

                return $requestMock;
            });

        $streamMock->expects($this->once())
            ->method("getContents")
            ->willReturn(json_encode(["data" => "hello, world."]));

        $responseMock->expects($this->once())
            ->method("getBody")
            ->willReturn($streamMock);

        $httpClientMock->expects($this->once())
            ->method("sendRequest")
            ->with($requestMock)
            ->willReturn($responseMock);

        $transport = new RequestTransport(
            $appId,
            $httpClientMock,
            $requestFactoryMock,
            $streamFactoryMock,
        );

        $result = $transport->transport($requestMock);

        $this->assertSame($result, [
            "data" => "hello, world."
        ]);
    }
}
