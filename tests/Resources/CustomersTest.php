<?php

namespace Tests\Resources;

use OpenPix\PhpSdk\RequestTransport;
use OpenPix\PhpSdk\Resources\Customers;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;

final class CustomersTest extends TestCase
{
    public function testList(): void
    {
        $requestTransportMock = $this->createMock(RequestTransport::class);
        $requestFactoryMock = $this->createMock(RequestFactoryInterface::class);
        $streamFactoryMock = $this->createMock(StreamFactoryInterface::class);

        $requestFactoryMock->expects($this->once())
            ->method("createRequest")
            ->with("GET", $this->stringContains("https://example.com/customer"));

        $customers = new Customers($requestTransportMock);

        $customers->list()->getPagedRequest()->build("https://example.com", $requestFactoryMock, $streamFactoryMock);
    }

    public function testGetOne()
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

        $correlationID = "fe7834b4060c488a9b0f89811be5f5cf";

        $requestFactoryMock->expects($this->once())
            ->method("createRequest")
            ->with("GET", "https://api.woovi.com/v1/customer/" . $correlationID)
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
            ->willReturn(json_encode(["customer" => []]));

        $customers = new Customers($requestTransport);

        $result = $customers->getOne($correlationID);

        $this->assertSame($result, ["customer" => []]);
    }

    public function testCreate()
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

        $customer = [
            "customer" => [
                "name" => "John Doe"
            ],
        ];

        $requestFactoryMock->expects($this->once())
            ->method("createRequest")
            ->with("POST", "https://api.woovi.com/v1/customer")
            ->willReturn($requestMock);

        $requestMock->expects($this->exactly(3))
            ->method("withAddedHeader")
            ->willReturnSelf();

        $streamFactoryMock->expects($this->once())
            ->method("createStream")
            ->with(json_encode($customer))
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
            ->willReturn(json_encode($customer));

        $customers = new Customers($requestTransport);

        $result = $customers->create($customer);

        $this->assertSame($result, $customer);
    }
}
