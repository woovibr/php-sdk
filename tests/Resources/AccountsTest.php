<?php

namespace Resources;

use OpenPix\PhpSdk\Request;
use OpenPix\PhpSdk\RequestTransport;
use OpenPix\PhpSdk\Resources\Accounts;
use PHPUnit\Framework\TestCase;

final class AccountsTest extends TestCase
{
    public function testList(): void
    {
        $accounts = [
            [
                "account" => [
                    "accountId" => "356a192b7913b04c54574d18c28d46e6395428ab",
                    "isDefault" => true,
                    "balance" => [
                        "total" => 129430,
                        "blocked" => 0,
                        "available" => 129430,
                    ],
                ],
            ],
            [
                "account" => [
                    "accountId" => "77de68daecd823babbb58edb1c8e14d7106e83bb",
                    "isDefault" => true,
                    "balance" => [
                        "total" => 3129430,
                        "blocked" => 0,
                        "available" => 3129430,
                    ],
                ],
            ]
        ];

        $requestTransportMock = $this->createMock(RequestTransport::class);
        $requestTransportMock->expects($this->once())
            ->method("transport")
            ->willReturnCallback(function (Request $request) use ($accounts) {
                $this->assertSame("GET", $request->getMethod());
                $this->assertSame("/api/v1/accounts", $request->getPath());
                $this->assertSame($request->getBody(), null);
                $this->assertSame($request->getQueryParams(), []);

                return $accounts;
            });

        $partners = new Accounts($requestTransportMock);

        $result = $partners->list();

        $this->assertSame($result, $accounts);
    }

    public function testGetOne(): void
    {
        $accountId = "356a192b7913b04c54574d18c28d46e6395428ab";
        $account = [
            "account" => [
                "accountId" => "356a192b7913b04c54574d18c28d46e6395428ab",
                "isDefault" => true,
                "balance" => [
                    "total" => 129430,
                    "blocked" => 0,
                    "available" => 129430,
                ],
            ],
        ];

        $requestTransportMock = $this->createMock(RequestTransport::class);
        $requestTransportMock->expects($this->once())
            ->method("transport")
            ->willReturnCallback(function (Request $request) use ($accountId, $account) {
                $this->assertSame("GET", $request->getMethod());
                $this->assertSame("/api/v1/account/" . $accountId, $request->getPath());
                $this->assertSame($request->getBody(), null);
                $this->assertSame($request->getQueryParams(), []);

                return $account;
            });

        $accounts = new Accounts($requestTransportMock);

        $result = $accounts->getOne($accountId);

        $this->assertSame($result, $account);
    }

    public function testWithdraw(): void
    {
        $accountId = "356a192b7913b04c54574d18c28d46e6395428ab";
        $value = 1000; // R$ 10,00

        $payload = [
            'value' => $value,
        ];

        $withdraw = [
            "withdraw" => [
                "account" => [
                    "accountId" => $accountId,
                    "isDefault" => true,
                    "balance" => [
                        "total" => 122430,
                        "blocked" => 0,
                        "available" => 122430,
                    ],
                ],
                "transaction" => [
                    "endToEndId" => "da4b9237bacccdf19c0760cab7aec4a8359010b0",
                    "transaction" => $value,
                ],
            ],
        ];

        $requestTransportMock = $this->createMock(RequestTransport::class);
        $requestTransportMock->expects($this->once())
            ->method("transport")
            ->willReturnCallback(function (Request $request) use ($accountId, $payload, $withdraw) {
                $this->assertSame("POST", $request->getMethod());
                $this->assertSame("/api/v1/account/" . $accountId . "/withdraw", $request->getPath());
                $this->assertSame($request->getBody(), $payload);
                $this->assertSame($request->getQueryParams(), []);

                return $withdraw;
            });

        $partners = new Accounts($requestTransportMock);

        $result = $partners->withdraw($accountId, $payload);

        $this->assertSame($result, $withdraw);
    }
}
