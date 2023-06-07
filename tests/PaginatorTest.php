<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use OpenPix\PhpSdk\Paginator;
use OpenPix\PhpSdk\Request;
use OpenPix\PhpSdk\RequestTransport;

final class PaginatorTest extends TestCase
{
    public function testGetPagedRequest(): void
    {
        $requestTransportMock = $this->createMock(RequestTransport::class);
        $listRequestMock = $this->createMock(Request::class);

        $listRequestMock->expects($this->once())
            ->method("pagination")
            ->with(20, 30);

        $paginator = new Paginator($requestTransportMock, $listRequestMock);

        $paginator->perPage(30)
            ->skip(20)
            ->getPagedRequest();
    }

    public function testSendRequest(): void
    {
        $requestTransportMock = $this->createMock(RequestTransport::class);
        $listRequestStub = $this->createStub(Request::class);

        $paginator = new Paginator($requestTransportMock, $listRequestStub);

        $requestTransportMock->expects($this->once())
            ->method("transport")
            ->with($paginator->getPagedRequest());

        $paginator->sendRequest();
    }

    public function testNext(): void
    {
        $requestTransportMock = $this->createMock(RequestTransport::class);
        $listRequestMock = $this->createMock(Request::class);

        $requestTransportMock->expects($this->once())
            ->method("transport")
            ->with($listRequestMock);

        $listRequestMock->expects($this->once())
            ->method("pagination")
            ->with(170, 50)
            ->willReturn($listRequestMock);

        $paginator = new Paginator($requestTransportMock, $listRequestMock);

        $paginator->perPage(50)
            ->skip(120)
            ->next();
    }

    public function testPrevious(): void
    {
        $requestTransportMock = $this->createMock(RequestTransport::class);
        $listRequestMock = $this->createMock(Request::class);

        $requestTransportMock->expects($this->once())
            ->method("transport")
            ->with($listRequestMock);

        $listRequestMock->expects($this->once())
            ->method("pagination")
            ->with(120, 50)
            ->willReturn($listRequestMock);

        $paginator = new Paginator($requestTransportMock, $listRequestMock);

        $paginator->perPage(50)
            ->skip(170)
            ->previous();
    }

    public function testGo(): void
    {
        $requestTransportMock = $this->createMock(RequestTransport::class);
        $listRequestMock = $this->createMock(Request::class);

        $requestTransportMock->expects($this->once())
            ->method("transport")
            ->with($listRequestMock);

        $listRequestMock->expects($this->once())
            ->method("pagination")
            ->with(50, 50)
            ->willReturn($listRequestMock);

        $paginator = new Paginator($requestTransportMock, $listRequestMock);

        $paginator->perPage(50)
            ->skip(0)
            ->go(1);
    }
}
