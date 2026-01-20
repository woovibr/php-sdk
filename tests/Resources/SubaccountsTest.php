<?php

namespace Resources;

use OpenPix\PhpSdk\Request;
use OpenPix\PhpSdk\RequestTransport;
use OpenPix\PhpSdk\Resources\Subaccounts;
use PHPUnit\Framework\TestCase;

final class SubaccountsTest extends TestCase
{
    public function testList(): void
    {
        $subaccountsResponse = [
            "subAccounts" => [
                "name" => "test-sub-account",
                "pixKey" => "c4249323-b4ca-43f2-8139-8232aab09b93",
                "balance" => 100
            ],
        ];

        $requestTransportMock = $this->createMock(RequestTransport::class);
        $requestTransportMock->expects($this->once())
            ->method("transport")
            ->willReturnCallback(function (Request $request) use ($subaccountsResponse) {
                $this->assertSame("GET", $request->getMethod());
                $this->assertSame("/api/v1/subaccount", $request->getPath());
                $this->assertSame($request->getBody(), null);
                $this->assertSame($request->getQueryParams(), []);

                return $subaccountsResponse;
            });

        $subaccounts = new Subaccounts($requestTransportMock);

        $result = $subaccounts->list();

        $this->assertSame($result, $subaccountsResponse);
    }

    public function testGetOne(): void
    {
        $subaccountId = "356a192b7913b04c54574d18c28d46e6395428ab";
        $subaccount = [
            "SubAccount" => [
                "name" => "test-sub-account",
                "pixKey" => $subaccountId,
                "balance" => 100,
            ],
        ];

        $requestTransportMock = $this->createMock(RequestTransport::class);
        $requestTransportMock->expects($this->once())
            ->method("transport")
            ->willReturnCallback(function (Request $request) use ($subaccountId, $subaccount) {
                $this->assertSame("GET", $request->getMethod());
                $this->assertSame("/api/v1/subaccount/" . $subaccountId, $request->getPath());
                $this->assertSame($request->getBody(), null);
                $this->assertSame($request->getQueryParams(), []);

                return $subaccount;
            });

        $subaccounts = new Subaccounts($requestTransportMock);

        $result = $subaccounts->getOne($subaccountId);

        $this->assertSame($result, $subaccount);
    }

    public function testDelete(): void
    {
        $subaccountId = "356a192b7913b04c54574d18c28d46e6395428ab";
        $response = [
            "status" => "OK",
            "pixKey" => "destination@test.com",
        ];

        $requestTransportMock = $this->createMock(RequestTransport::class);
        $requestTransportMock->expects($this->once())
            ->method("transport")
            ->willReturnCallback(function (Request $request) use ($subaccountId, $response) {
                $this->assertSame("DELETE", $request->getMethod());
                $this->assertSame("/api/v1/subaccount/" . $subaccountId, $request->getPath());
                $this->assertSame($request->getBody(), null);
                $this->assertSame($request->getQueryParams(), []);

                return $response;
            });

        $subaccounts = new Subaccounts($requestTransportMock);

        $result = $subaccounts->delete($subaccountId);

        $this->assertSame($result, $response);
    }

    public function testWithdraw(): void
    {
        $subaccountId = "356a192b7913b04c54574d18c28d46e6395428ab";
        $value = 1000; // R$ 10,00

        $payload = [
            'value' => $value,
        ];

        $withdraw = [
            "transaction" => [
                "status" => "CREATED",
                "value" => 100,
                "endToEndId" => "ENDTOENDID_1234567890",
                "correlationID" => "TESTING1323",
                "destinationAlias" => "pixKeyTest@test.com",
                "comment" => "testing-transaction",
            ],
        ];

        $requestTransportMock = $this->createMock(RequestTransport::class);
        $requestTransportMock->expects($this->once())
            ->method("transport")
            ->willReturnCallback(function (Request $request) use ($subaccountId, $payload, $withdraw) {
                $this->assertSame("POST", $request->getMethod());
                $this->assertSame("/api/v1/subaccount/" . $subaccountId . "/withdraw", $request->getPath());
                $this->assertSame($request->getBody(), $payload);
                $this->assertSame($request->getQueryParams(), []);

                return $withdraw;
            });

        $subaccounts = new Subaccounts($requestTransportMock);

        $result = $subaccounts->withdraw($subaccountId, $payload);

        $this->assertSame($result, $withdraw);
    }

    public function testDebitToMainAccount(): void
    {
        $subaccountId = "356a192b7913b04c54574d18c28d46e6395428ab";
        $value = 1000; // R$ 10,00

        $payload = [
            "value" => $value,
            "description" => "Optional description for the debit operation",
        ];

        $debitResponse = [
            "pixKey" => "subaccount@test.com",
            "value" => 50,
            "description" => "Monthly payment",
            "success" => "Sub-account withdrawal has been successfully debited, 50"
        ];

        $requestTransportMock = $this->createMock(RequestTransport::class);
        $requestTransportMock->expects($this->once())
            ->method("transport")
            ->willReturnCallback(function (Request $request) use ($subaccountId, $payload, $debitResponse) {
                $this->assertSame("POST", $request->getMethod());
                $this->assertSame("/api/v1/subaccount/" . $subaccountId . "/debit", $request->getPath());
                $this->assertSame($request->getBody(), $payload);
                $this->assertSame($request->getQueryParams(), []);

                return $debitResponse;
            });

        $subaccounts = new Subaccounts($requestTransportMock);

        $result = $subaccounts->debitToMainAccount($subaccountId, $payload);

        $this->assertSame($result, $debitResponse);
    }

    public function testTransferBetweenSubaccounts(): void
    {
        $value = 1000; // R$ 10,00

        $payload = [
            "value" => $value,
            "fromPixKey" => "3143da48-2bc7-49a4-89bd-4e22f73bfb0c",
            "fromPixKeyType" => "RANDOM",
            "toPixKey" => "c4249323-b4ca-43f2-8139-874baab09b93",
            "toPixKeyType" => "RANDOM",
            "correlationID" => "unique-id",
        ];

        $transferResponse = [
            "value" => $value,
            "destinationSubaccount" => [
                "name" => "test-sub-account-1",
                "pixKey" => "c4249323-b4ca-43f2-8139-874baab09b93",
                "balance" => 1000
            ],
            "originSubaccount" => [
                "name" => "test-sub-account-2",
                "pixKey" => "3143da48-2bc7-49a4-89bd-4e22f73bfb0c",
                "balance" => 0,
            ],
        ];

        $requestTransportMock = $this->createMock(RequestTransport::class);
        $requestTransportMock->expects($this->once())
            ->method("transport")
            ->willReturnCallback(function (Request $request) use ($payload, $transferResponse) {
                $this->assertSame("POST", $request->getMethod());
                $this->assertSame("/api/v1/subaccount/transfer", $request->getPath());
                $this->assertSame($request->getBody(), $payload);
                $this->assertSame($request->getQueryParams(), []);

                return $transferResponse;
            });

        $subaccounts = new Subaccounts($requestTransportMock);

        $result = $subaccounts->transferBetweenSubaccounts($payload);

        $this->assertSame($result, $transferResponse);
    }

    public function testCreate(): void
    {
        $payload = [
            "name" => "Name of subaccount",
            "pixKey" => "356a192b7913b04c54574d18c28d46e6395428ab",
        ];

        $createResponse = [
            "SubAccount" => [
                "name" => "Name of subaccount",
                "pixKey" => "356a192b7913b04c54574d18c28d46e6395428ab",
            ],
        ];

        $requestTransportMock = $this->createMock(RequestTransport::class);
        $requestTransportMock->expects($this->once())
            ->method("transport")
            ->willReturnCallback(function (Request $request) use ($payload, $createResponse) {
                $this->assertSame("POST", $request->getMethod());
                $this->assertSame("/api/v1/subaccount", $request->getPath());
                $this->assertSame($request->getBody(), $payload);
                $this->assertSame($request->getQueryParams(), []);

                return $createResponse;
            });

        $subaccounts = new Subaccounts($requestTransportMock);

        $result = $subaccounts->create($payload);

        $this->assertSame($result, $createResponse);
    }
}
