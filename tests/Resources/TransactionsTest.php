<?php

namespace Tests;

use OpenPix\PhpSdk\Request;
use OpenPix\PhpSdk\RequestTransport;
use OpenPix\PhpSdk\Resources\Transactions;
use PHPUnit\Framework\TestCase;

final class TransactionsTest extends TestCase
{
    public function testGetOne()
    {
        $requestTransportMock = $this->createMock(RequestTransport::class);

        $requestTransportMock->expects($this->once())
            ->method("transport")
            ->willReturnCallback(function (Request $request) {
                $this->assertSame("GET", $request->getMethod());
                $this->assertSame("/transaction/1", $request->getPath());
                $this->assertSame($request->getBody(), null);
                $this->assertSame($request->getQueryParams(), []);

                return ["transaction" => []];
            });

        $transactions = new Transactions($requestTransportMock);

        $result = $transactions->getOne(1);

        $this->assertSame($result, ["transaction" => []]);
    }
}
