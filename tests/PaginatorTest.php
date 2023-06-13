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

        $paginator = $this->makePaginator(null, $listRequestMock);

        $paginator->perPage(30)->skip(20)->getPagedRequest();
    }

    public function testNext(): void
    {
        $this->testPaginatorNavigation(
            fn (Paginator $paginator) => $paginator->next(),
            30
        );
    }

    public function testPrevious(): void
    {
        $this->testPaginatorNavigation(
            fn (Paginator $paginator) => $paginator->previous(),
            0,
            30
        );
    }

    public function testGo(): void
    {
        $this->testPaginatorNavigation(
            fn (Paginator $paginator) => $paginator->go(2),
            60
        );
    }

    public function testRewind(): void
    {
        $this->testPaginatorNavigation(fn (Paginator $paginator) => $paginator->rewind());
    }

    public function testKey(): void
    {
        $paginator = $this->makePaginator()->perPage(30)->skip(50);

        $this->assertSame(1, $paginator->key());
    }

    public function testCurrent(): void
    {
        $listRequestMock = $this->createMock(Request::class);
        $listRequestMock->expects($this->once())
            ->method("pagination")
            ->willReturnSelf();

        $requestTransportMock = $this->createMock(RequestTransport::class);
        $requestTransportMock->expects($this->once())
            ->method("transport")
            ->with($listRequestMock);

        $paginator = new Paginator($requestTransportMock, $listRequestMock);

        $paginator->current();
    }

    public function testValid(): void
    {
        $paginator = $this->makePaginator(["pageInfo" => ["hasNextPage" => false]]);

        $this->assertFalse($paginator->valid());
    }

    public function testGetTotalResourcesCount(): void
    {
        $paginator = $this->makePaginator(["pageInfo" => ["totalCount" => 10]]);

        $this->assertSame(10, $paginator->getTotalResourcesCount());
    }

    private function testPaginatorNavigation(callable $navigate, int $expectedSkip = 0, int $skip = 0, int $perPage = 30): void
    {
        $paginator = $this->makePaginator();

        $paginator->skip($skip)->perPage($perPage);

        $navigate($paginator);

        $this->assertSame($expectedSkip, $paginator->getSkippedCount());
    }

    /**
     * @param array<mixed> $lastResult
     */
    private function makePaginator(
        ?array $lastResult = null,
        ?Request $request = null,
        ?RequestTransport $requestTransport = null
    ): Paginator
    {
        return new Paginator(
            $requestTransport ?? $this->createMock(RequestTransport::class),
            $request ?? $this->createMock(Request::class),
            $lastResult
        );
    }
}
