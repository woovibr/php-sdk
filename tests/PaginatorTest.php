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
            function (Paginator $paginator): void {
                $paginator->next();
            },
            30
        );
    }

    public function testPrevious(): void
    {
        $this->testPaginatorNavigation(
            function (Paginator $paginator): void {
                $paginator->previous();
            },
            0,
            30
        );
    }

    public function testGo(): void
    {
        $this->testPaginatorNavigation(
            function (Paginator $paginator): void {
                $paginator->go(2);
            },
            60
        );
    }

    public function testRewind(): void
    {
        $this->testPaginatorNavigation(
            function (Paginator $paginator): void {
                $paginator->rewind();
            }
        );
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
        $paginator = $this->makePaginator();
        $this->assertTrue($paginator->valid(), 'Should be valid when lastResult is null');

        $singlePageResult = [
            "pageInfo" => [
                "skip" => 0,
                "limit" => 30,
                "totalCount" => 7,
                "hasNextPage" => false,
                "hasPreviousPage" => false
            ]
        ];
        $paginator = $this->makePaginator($singlePageResult);
        $this->assertTrue($paginator->valid(), 'Should be valid for single page with data');

        $paginator = $this->makePaginator($singlePageResult);
        $paginator->skip(10);
        $this->assertFalse($paginator->valid(), 'Should be invalid when skip > totalCount');
    }

    public function testGetTotalResourcesCount(): void
    {
        $paginator = $this->makePaginator(["pageInfo" => ["totalCount" => 10]]);

        $this->assertSame(10, $paginator->getTotalResourcesCount());
    }

    public function testSinglePageIteration(): void
    {
        $singlePageResponse = [
            'transactions' => [
                ['id' => 1, 'amount' => 100],
                ['id' => 2, 'amount' => 200],
                ['id' => 3, 'amount' => 300],
                ['id' => 4, 'amount' => 400],
                ['id' => 5, 'amount' => 500],
                ['id' => 6, 'amount' => 600],
                ['id' => 7, 'amount' => 700],
            ],
            'pageInfo' => [
                'skip' => 0,
                'limit' => 30,
                'totalCount' => 7,
                'hasPreviousPage' => false,
                'hasNextPage' => false,
            ]
        ];

        $listRequestMock = $this->createMock(Request::class);
        $listRequestMock->expects($this->once())
            ->method("pagination")
            ->willReturnSelf();

        $requestTransportMock = $this->createMock(RequestTransport::class);
        $requestTransportMock->expects($this->once())
            ->method('transport')
            ->with($listRequestMock)
            ->willReturn($singlePageResponse);

        $paginator = new Paginator($requestTransportMock, $listRequestMock);

        $iterations = 0;
        $processedTransactions = [];

        foreach ($paginator as $result)
        {
            $iterations++;
            $this->assertArrayHasKey('transactions', $result);
            $this->assertArrayHasKey('pageInfo', $result);
            $this->assertCount(7, $result['transactions']);

            foreach ($result['transactions'] as $transaction)
            {
                $processedTransactions[] = $transaction;
            }
        }

        $this->assertSame(1, $iterations, 'Should iterate exactly once for single page');
        $this->assertCount(7, $processedTransactions, 'Should process all 7 transactions');
        $this->assertSame(100, $processedTransactions[0]['amount'], 'First transaction should be accessible');
        $this->assertSame(700, $processedTransactions[6]['amount'], 'Last transaction should be accessible');
    }

    public function testMultiPageIncludesLastPage(): void
    {
        $responses = [
            [
                'charges' => [
                    ['id' => 1, 'amount' => 100],
                    ['id' => 2, 'amount' => 200],
                ],
                'pageInfo' => [
                    'skip' => 0,
                    'limit' => 2,
                    'totalCount' => 5,
                    'hasPreviousPage' => false,
                    'hasNextPage' => true
                ]
            ],
            [
                'charges' => [
                    ['id' => 3, 'amount' => 300],
                    ['id' => 4, 'amount' => 400],
                ],
                'pageInfo' => [
                    'skip' => 2,
                    'limit' => 2,
                    'totalCount' => 5,
                    'hasPreviousPage' => true,
                    'hasNextPage' => true
                ]
            ],
            [
                'charges' => [
                    ['id' => 5, 'amount' => 500],
                ],
                'pageInfo' => [
                    'skip' => 4,
                    'limit' => 2,
                    'totalCount' => 5,
                    'hasPreviousPage' => true,
                    'hasNextPage' => false  // Last page
                ]
            ]
        ];

        $listRequestMock = $this->createMock(Request::class);
        $listRequestMock->expects($this->exactly(3))
            ->method("pagination")
            ->willReturnSelf();

        $requestTransportMock = $this->createMock(RequestTransport::class);
        $requestTransportMock->expects($this->exactly(3))
            ->method('transport')
            ->with($listRequestMock)
            ->willReturnOnConsecutiveCalls(...$responses);

        $paginator = new Paginator($requestTransportMock, $listRequestMock);
        $paginator->perPage(2); // Set page size to 2 to create 3 pages

        $iterations = 0;
        $allCharges = [];

        foreach ($paginator as $result)
        {
            $iterations++;
            $this->assertArrayHasKey('charges', $result);

            foreach ($result['charges'] as $charge)
            {
                $allCharges[] = $charge;
            }
        }

        $this->assertSame(3, $iterations, 'Should iterate through all 3 pages');
        $this->assertCount(5, $allCharges, 'Should process all 5 charges including last page');

        $lastCharge = end($allCharges);
        $this->assertSame(5, $lastCharge['id'], 'Should include charge from last page');
        $this->assertSame(500, $lastCharge['amount'], 'Last page data should be accessible');
    }

    public function testEmptyResultsIteration(): void
    {
        $emptyResponse = [
            'items' => [],
            'pageInfo' => [
                'skip' => 0,
                'limit' => 30,
                'totalCount' => 0,
                'hasPreviousPage' => false,
                'hasNextPage' => false
            ]
        ];

        $listRequestMock = $this->createMock(Request::class);
        $listRequestMock->expects($this->once())
            ->method("pagination")
            ->willReturnSelf();

        $requestTransportMock = $this->createMock(RequestTransport::class);
        $requestTransportMock->expects($this->once())
            ->method('transport')
            ->with($listRequestMock)
            ->willReturn($emptyResponse);

        $paginator = new Paginator($requestTransportMock, $listRequestMock);

        $iterations = 0;
        foreach ($paginator as $result)
        {
            $iterations++;
            $this->assertArrayHasKey('items', $result);
            $this->assertEmpty($result['items']);
        }

        $this->assertSame(1, $iterations, 'Should iterate once even for empty results');
    }

    public function testOriginalBugReportScenario(): void
    {
        $getTransactionsResponse = [
            'transactions' => [
                ['id' => 't1', 'amount' => 1000, 'date' => '2024-01-01'],
                ['id' => 't2', 'amount' => 2000, 'date' => '2024-01-02'],
                ['id' => 't3', 'amount' => 3000, 'date' => '2024-01-03'],
                ['id' => 't4', 'amount' => 4000, 'date' => '2024-01-04'],
                ['id' => 't5', 'amount' => 5000, 'date' => '2024-01-05'],
                ['id' => 't6', 'amount' => 6000, 'date' => '2024-01-06'],
                ['id' => 't7', 'amount' => 7000, 'date' => '2024-01-07'],
            ],
            'pageInfo' => [
                'skip' => 0,
                'limit' => 30,
                'totalCount' => 7,
                'hasPreviousPage' => false,
                'hasNextPage' => false
            ]
        ];

        $listRequestMock = $this->createMock(Request::class);
        $listRequestMock->expects($this->once())
            ->method("pagination")
            ->willReturnSelf();

        $requestTransportMock = $this->createMock(RequestTransport::class);
        $requestTransportMock->expects($this->once())
            ->method('transport')
            ->with($listRequestMock)
            ->willReturn($getTransactionsResponse);

        $paginator = new Paginator($requestTransportMock, $listRequestMock);

        $outerLoopExecuted = false;
        $transactionIds = [];

        foreach ($paginator as $result)
        {
            $outerLoopExecuted = true;

            foreach ($result['transactions'] as $transaction)
            {
                $transactionIds[] = $transaction['id'];
            }
        }

        $this->assertTrue($outerLoopExecuted, 'Outer foreach loop must execute');
        $this->assertCount(7, $transactionIds, 'Should access all 7 transactions');
        $this->assertSame(['t1', 't2', 't3', 't4', 't5', 't6', 't7'], $transactionIds);
    }

    public function testValidWithDifferentSkipPositions(): void
    {
        $result = [
            "pageInfo" => [
                "skip" => 0,
                "limit" => 10,
                "totalCount" => 25,
                "hasNextPage" => true,
                "hasPreviousPage" => false
            ]
        ];

        $paginator = $this->makePaginator($result);
        $paginator->skip(0);
        $this->assertTrue($paginator->valid(), 'Should be valid at position 0');

        $paginator = $this->makePaginator($result);
        $paginator->skip(15);
        $this->assertTrue($paginator->valid(), 'Should be valid when skip < totalCount');

        $paginator = $this->makePaginator($result);
        $paginator->skip(24);
        $this->assertTrue($paginator->valid(), 'Should be valid when skip = totalCount - 1');

        $paginator = $this->makePaginator($result);
        $paginator->skip(25);
        $this->assertFalse($paginator->valid(), 'Should be invalid when skip >= totalCount');

        $paginator = $this->makePaginator($result);
        $paginator->skip(30);
        $this->assertFalse($paginator->valid(), 'Should be invalid when skip > totalCount');
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
    ): Paginator {
        return new Paginator(
            $requestTransport ?? $this->createMock(RequestTransport::class),
            $request ?? $this->createMock(Request::class),
            $lastResult
        );
    }
}
