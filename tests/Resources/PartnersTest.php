<?php

namespace Tests\Resources;

use OpenPix\PhpSdk\Request;
use OpenPix\PhpSdk\RequestTransport;
use OpenPix\PhpSdk\Resources\Partners;
use PHPUnit\Framework\TestCase;

final class PartnersTest extends TestCase
{
    public function testList(): void
    {
        $requestTransportMock = $this->createMock(RequestTransport::class);

        $partners = new Partners($requestTransportMock);

        $pagedRequest = $partners->list()->getPagedRequest();

        $this->assertSame($pagedRequest->getPath(), "/api/v1/partner/company");
        $this->assertSame($pagedRequest->getMethod(), "GET");
        $this->assertSame($pagedRequest->getBody(), null);
    }

    public function testGetOne(): void
    {
        $taxID = "11111111111111";
        $partner = [
            "preRegistration" => [
                "preRegistration" => [
                    "name" => "string",
                ],
                "user" => [
                    "firstName" => "string",
                ],
            ],
        ];

        $requestTransportMock = $this->createMock(RequestTransport::class);
        $requestTransportMock->expects($this->once())
            ->method("transport")
            ->willReturnCallback(function (Request $request) use ($taxID, $partner) {
                $this->assertSame("GET", $request->getMethod());
                $this->assertSame("/api/v1/partner/company/" . $taxID, $request->getPath());
                $this->assertSame($request->getBody(), null);
                $this->assertSame($request->getQueryParams(), []);

                return $partner;
            });

        $partners = new Partners($requestTransportMock);

        $result = $partners->getOne($taxID);

        $this->assertSame($result, $partner);
    }

    public function testCreate(): void
    {
        $partner = [
            "preRegistration" => [
                "name" => "Example LLC",
            ],
            "user" => [
                "firstName" => "John",
            ],
        ];

        $requestTransportMock = $this->createMock(RequestTransport::class);
        $requestTransportMock->expects($this->once())
            ->method("transport")
            ->willReturnCallback(function (Request $request) use ($partner) {
                $this->assertSame("POST", $request->getMethod());
                $this->assertSame("/api/v1/partner/company", $request->getPath());
                $this->assertSame($request->getBody(), $partner);
                $this->assertSame($request->getQueryParams(), []);

                return $partner;
            });

        $partners = new Partners($requestTransportMock);

        $result = $partners->create($partner);

        $this->assertSame($result, $partner);
    }

    public function testCreateApp(): void
    {
        $partner = [
            "application" => [
                "name" => "Example LLC",
                "type" => "API",
            ],
            "taxID" => [
                "taxID" => "65914571000187",
                "type" => "BR:CNPJ",
            ],
        ];

        $requestTransportMock = $this->createMock(RequestTransport::class);
        $requestTransportMock->expects($this->once())
            ->method("transport")
            ->willReturnCallback(function (Request $request) use ($partner) {
                $this->assertSame("POST", $request->getMethod());
                $this->assertSame("/api/v1/partner/application", $request->getPath());
                $this->assertSame($request->getBody(), $partner);
                $this->assertSame($request->getQueryParams(), []);

                return $partner;
            });

        $partners = new Partners($requestTransportMock);

        $result = $partners->createApp($partner);

        $this->assertSame($result, $partner);
    }
}
