<?php

namespace Tests\Resources;

use OpenPix\PhpSdk\Request;
use OpenPix\PhpSdk\RequestTransport;
use OpenPix\PhpSdk\Resources\Customers;
use PHPUnit\Framework\TestCase;

final class CustomersTest extends TestCase
{
    public function testList(): void
    {
        $requestTransportMock = $this->createMock(RequestTransport::class);

        $customers = new Customers($requestTransportMock);

        $pagedRequest = $customers->list()->getPagedRequest();

        $this->assertSame($pagedRequest->getPath(), "/customer");
        $this->assertSame($pagedRequest->getMethod(), "GET");
        $this->assertSame($pagedRequest->getBody(), null);
    }

    public function testGetOne()
    {
        $correlationID = "fe7834b4060c488a9b0f89811be5f5cf";
        $customer = [
            "customer" => [
                "name" => "John Doe",
            ],
        ];

        $requestTransportMock = $this->createMock(RequestTransport::class);
        $requestTransportMock->expects($this->once())
            ->method("transport")
            ->willReturnCallback(function (Request $request) use ($correlationID, $customer) {
                $this->assertSame("GET", $request->getMethod());
                $this->assertSame("/customer/" . $correlationID, $request->getPath());
                $this->assertSame($request->getBody(), null);
                $this->assertSame($request->getQueryParams(), []);

                return $customer;
            });

        $customers = new Customers($requestTransportMock);

        $result = $customers->getOne($correlationID);

        $this->assertSame($result, $customer);
    }

    public function testCreate()
    {
        $customer = [
            "customer" => [
                "name" => "John Doe"
            ],
        ];

        $requestTransportMock = $this->createMock(RequestTransport::class);
        $requestTransportMock->expects($this->once())
            ->method("transport")
            ->willReturnCallback(function (Request $request) use ($customer) {
                $this->assertSame("POST", $request->getMethod());
                $this->assertSame("/customer", $request->getPath());
                $this->assertSame($request->getBody(), $customer);
                $this->assertSame($request->getQueryParams(), []);

                return $customer;
            });

        $customers = new Customers($requestTransportMock);

        $result = $customers->create($customer);

        $this->assertSame($result, $customer);
    }
}
