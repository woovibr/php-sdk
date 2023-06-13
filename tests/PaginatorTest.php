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
        $listRequestMock = $this->createMock(Request::class);
        $listRequestMock->expects($this->once())
            ->method("pagination")
            ->with(20, 30);

        $paginator = new Paginator($this->createMock(RequestTransport::class), $listRequestMock);

        $paginator->perPage(30)->skip(20)->getPagedRequest();
    }

    public function testUpdate(): void
    {
        $this->getPaginatorForUpdateTests()->update();
    }

    public function testNext(): void
    {
        $this->getPaginatorForUpdateTests(30)->next();
    }

    public function testPrevious(): void
    {
        $this->getPaginatorForUpdateTests(0, 30, 30)->previous();
    }

    public function testGo(): void
    {
        $this->getPaginatorForUpdateTests(30)->go(1);
    }

    public function testRewind(): void
    {
        $this->getPaginatorForUpdateTests(0, 30, 30)->rewind();
    }

    public function testKey(): void
    {
        $paginator = new Paginator($this->createMock(RequestTransport::class), $this->createMock(Request::class));

        $paginator->perPage(30)->skip(50);

        $this->assertSame(1, $paginator->key());
    }

    public function testCurrent(): void
    {
        $paginator = $this->getPaginatorForUpdateTests();

        $paginator->current();

        // Should not send request if there is already a previous result
        // It will fail if it sends more than one request to the request transport
        $paginator->current();
    }

    public function testValid(): void
    {
        $makePaginator = fn ($hasPreviousPage, $hasNextPage) => new Paginator(
            $this->createMock(RequestTransport::class),
            $this->createMock(Request::class),
            [
                "pageInfo" => [
                    "hasPreviousPage" => $hasPreviousPage,
                    "hasNextPage" => $hasNextPage,
                ],
            ]
        );

        $this->assertTrue($makePaginator(true, true)->valid());
        $this->assertTrue($makePaginator(true, false)->valid());
        $this->assertTrue($makePaginator(false, true)->valid());
        $this->assertFalse($makePaginator(false, false)->valid());
    }

    public function testGetTotalResourcesCount(): void
    {
        $paginator = new Paginator(
            $this->createMock(RequestTransport::class),
            $this->createMock(Request::class),
            [
                "pageInfo" => [
                    "totalCount" => 10,
                ],
            ]
        );

        $this->assertSame(10, $paginator->getTotalResourcesCount());
    }

    /**
     * Get a paginator with expectations set regarding the "skip" and "limit"
     * parameters of the requests.
     *
     * Must run the method under test when you get the paginator (the "Act" of AAA).
     */
    private function getPaginatorForUpdateTests(int $expectedSkip = 0, int $expectedLimit = 30, int $skip = 0, int $perPage = 30): Paginator
    {
        $listRequestMock = $this->createMock(Request::class);
        $listRequestMock->expects($this->once())
            ->method("pagination")
            ->with($expectedSkip, $expectedLimit)
            ->willReturn($listRequestMock);

        $requestTransportMock = $this->createMock(RequestTransport::class);
        $requestTransportMock->expects($this->once())
            ->method("transport")
            ->with($listRequestMock);

        return (new Paginator($requestTransportMock, $listRequestMock))
            ->skip($skip)
            ->perPage($perPage);
    }
}
