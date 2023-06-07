<?php

namespace Tests\Resources;

use OpenPix\PhpSdk\RequestTransport;
use OpenPix\PhpSdk\Resources\Customers;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

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
}
