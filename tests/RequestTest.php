<?php

namespace Tests;

use OpenPix\PhpSdk\Request;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;

final class RequestTest extends TestCase
{
    public function testShouldBuildWithArrayBody(): void
    {
        $body = ["name" => "John Doe"];
        $queryParams = [
            "foo" => "bar",
            "baz" => "foz"
        ];
        $path = "/costumer";
        $method = "POST";

        $baseUri = "https://example.com";

        $stream = $this->createStub(StreamInterface::class);
        $stream->method("getContents")
            ->willReturn(json_encode($body));

        $streamFactory = $this->createMock(StreamFactoryInterface::class);
        $streamFactory->expects($this->once())
            ->method("createStream")
            ->with(json_encode($body))
            ->willReturn($stream);

        $psrRequestMock = $this->createMock(RequestInterface::class);
        $psrRequestMock->expects($this->once())
            ->method("withBody")
            ->with($stream)
            ->willReturnSelf();

        $psrRequestMock->expects($this->once())
            ->method("withAddedHeader")
            ->with("Content-type", "application/json")
            ->willReturnSelf();

        $requestFactory = $this->createMock(RequestFactoryInterface::class);
        $requestFactory->expects($this->once())
            ->method("createRequest")
            ->with($method, $baseUri . $path . "?foo=bar&baz=foz")
            ->willReturn($psrRequestMock);

        $result = (new Request())
            ->path($path)
            ->method($method)
            ->body($body)
            ->queryParams($queryParams)
            ->build($baseUri, $requestFactory, $streamFactory);

        $this->assertSame($result, $psrRequestMock);
    }

    public function testShouldApplyPagination(): void
    {
        $streamFactory = $this->createMock(StreamFactoryInterface::class);
        $requestFactory = $this->createMock(RequestFactoryInterface::class);

        $requestFactory->expects($this->once())
            ->method("createRequest")
            ->with("GET", "https://example.com/costumer?skip=100&limit=200");

        (new Request())
            ->path("/costumer")
            ->method("GET")
            ->pagination(100, 200)
            ->build("https://example.com", $requestFactory, $streamFactory);
    }
}
