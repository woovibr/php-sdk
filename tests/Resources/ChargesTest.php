<?php

namespace Tests;

use OpenPix\PhpSdk\RequestTransport;
use OpenPix\PhpSdk\Resources\Charges;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;

final class ChargesTest extends TestCase
{
    public function testGetOne(): void
    {
        $requestFactoryMock = $this->createMock(RequestFactoryInterface::class);
        $streamFactoryMock = $this->createMock(StreamFactoryInterface::class);
        $httpClientMock = $this->createMock(ClientInterface::class);
        $requestMock = $this->createMock(RequestInterface::class);
        $responseMock = $this->createMock(ResponseInterface::class);
        $streamMock = $this->createMock(StreamInterface::class);

        $requestTransport = new RequestTransport(
            "app id",
            $httpClientMock,
            $requestFactoryMock,
            $streamFactoryMock,
        );

        $chargeId = "fe7834b4060c488a9b0f89811be5f5cf";

        $requestFactoryMock->expects($this->once())
            ->method("createRequest")
            ->with("GET", "https://api.woovi.com/api/v1/charge/" . $chargeId)
            ->willReturn($requestMock);

        $requestMock->expects($this->exactly(2))
            ->method("withAddedHeader")
            ->willReturnSelf();

        $httpClientMock->expects($this->once())
            ->method("sendRequest")
            ->with($requestMock)
            ->willReturn($responseMock);

        $responseMock->expects($this->once())
            ->method("getBody")
            ->willReturn($streamMock);

        $streamMock->expects($this->once())
            ->method("getContents")
            ->willReturn(json_encode(["charge" => []]));

        $charges = new Charges($requestTransport);

        $result = $charges->getOne($chargeId);

        $this->assertSame($result, ["charge" => []]);
    }

    public function testCreate(): void
    {
        $requestFactoryMock = $this->createMock(RequestFactoryInterface::class);
        $streamFactoryMock = $this->createMock(StreamFactoryInterface::class);
        $httpClientMock = $this->createMock(ClientInterface::class);
        $requestMock = $this->createMock(RequestInterface::class);
        $requestStreamMock = $this->createMock(StreamInterface::class);
        $responseMock = $this->createMock(ResponseInterface::class);
        $responseStreamMock = $this->createMock(StreamInterface::class);

        $requestTransport = new RequestTransport(
            "app id",
            $httpClientMock,
            $requestFactoryMock,
            $streamFactoryMock,
        );

        $charge = [
            "correlationID" => "fe7834b4060c488a9b0f89811be5f5cf",
            "value" => 100,
        ];

        $requestFactoryMock->expects($this->once())
            ->method("createRequest")
            ->with("POST", "https://api.woovi.com/api/v1/charge?return_existing=true")
            ->willReturn($requestMock);

        $requestMock->expects($this->exactly(3))
            ->method("withAddedHeader")
            ->willReturnSelf();

        $streamFactoryMock->expects($this->once())
            ->method("createStream")
            ->with(json_encode($charge))
            ->willReturn($requestStreamMock);

        $requestMock->expects($this->once())
            ->method("withBody")
            ->with($requestStreamMock)
            ->willReturnSelf();

        $httpClientMock->expects($this->once())
            ->method("sendRequest")
            ->with($requestMock)
            ->willReturn($responseMock);

        $responseMock->expects($this->once())
            ->method("getBody")
            ->willReturn($responseStreamMock);

        $responseStreamMock->expects($this->once())
            ->method("getContents")
            ->willReturn(json_encode($charge));

        $charges = new Charges($requestTransport);

        $result = $charges->create($charge, true);

        $this->assertSame($result, $charge);
    }

    public function testList(): void
    {
        $requestTransportMock = $this->createMock(RequestTransport::class);
        $requestFactoryMock = $this->createMock(RequestFactoryInterface::class);
        $streamFactoryMock = $this->createMock(StreamFactoryInterface::class);

        $requestFactoryMock->expects($this->once())
            ->method("createRequest")
            ->with("GET", $this->stringContains("https://example.com/charge"));

        $charges = new Charges($requestTransportMock);

        $charges->list()->getPagedRequest()->build("https://example.com", $requestFactoryMock, $streamFactoryMock);
    }

    public function testDelete(): void
    {
        $requestFactoryMock = $this->createMock(RequestFactoryInterface::class);
        $streamFactoryMock = $this->createMock(StreamFactoryInterface::class);
        $httpClientMock = $this->createMock(ClientInterface::class);
        $requestMock = $this->createMock(RequestInterface::class);
        $responseMock = $this->createMock(ResponseInterface::class);
        $streamMock = $this->createMock(StreamInterface::class);

        $requestTransport = new RequestTransport(
            "app id",
            $httpClientMock,
            $requestFactoryMock,
            $streamFactoryMock,
        );

        $chargeId = "fe7834b4060c488a9b0f89811be5f5cf";

        $requestFactoryMock->expects($this->once())
            ->method("createRequest")
            ->with("GET", "https://api.woovi.com/api/v1/charge/" . $chargeId)
            ->willReturn($requestMock);

        $requestMock->expects($this->exactly(2))
            ->method("withAddedHeader")
            ->willReturnSelf();

        $httpClientMock->expects($this->once())
            ->method("sendRequest")
            ->with($requestMock)
            ->willReturn($responseMock);

        $responseMock->expects($this->once())
            ->method("getBody")
            ->willReturn($streamMock);

        $streamMock->expects($this->once())
            ->method("getContents")
            ->willReturn(json_encode(["id" => $chargeId]));

        $charges = new Charges($requestTransport);

        $result = $charges->getOne($chargeId);

        $this->assertSame($result, ["id" => $chargeId]);
    }

    public function testGetQrCodeImageLink()
    {
        $charges = new Charges($this->createStub(RequestTransport::class));

        $result = $charges->getQrCodeImageLink("123456", 256);

        $this->assertSame("https://api.woovi.com/openpix/charge/brcode/image/123456.png?size=256", $result);
    }
}
