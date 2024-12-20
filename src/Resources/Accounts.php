<?php

namespace OpenPix\PhpSdk\Resources;

use OpenPix\PhpSdk\Request;
use OpenPix\PhpSdk\RequestTransport;

/**
 * Operations on accounts.
 *
 * @link https://developers.openpix.com.br/api#tag/account
 */
class Accounts
{
    /**
     * Used to send HTTP requests to accounts API.
     *
     * @var RequestTransport
     */
    private $requestTransport;

    /**
     * Create a new Accounts instance.
     *
     * @param RequestTransport $requestTransport Used to send HTTP requests to accounts API.
     */
    public function __construct(RequestTransport $requestTransport)
    {
        $this->requestTransport = $requestTransport;
    }

    /**
     * Get a list of Accounts.
     *
     * ## Usage
     * ```php
     * $result = $client->accounts()->list();
     *
     * foreach ($result['accounts'] as $account) {
     *     $account["accountId"]; // string
     *     $account["isDefault"]; // boolean
     *     $account["balance"]; // array
     *     $account["balance"]["total"]; // int
     *     $account["balance"]["blocked"]; // int
     *     $account["balance"]["available"]; // int
     * }
     * ```
     *
     * @link https://developers.openpix.com.br/api#tag/account/paths/~1api~1v1~1account~1/get
     *
     * @return array<string, mixed> Result from API.
     */
    public function list(): array
    {
        $request = (new Request())
            ->method("GET")
            ->path("/api/v1/accounts");

        return $this->requestTransport->transport($request);
    }

    /**
     * Get an Account via accountId param.
     *
     * ```php
     * $result = $client->accounts()->getOne("accountId");
     *
     * $result["account"]["accountId"]; // string
     * $result["account"]["isDefault"]; // boolean
     * $result["account"]["balance"]; // array
     * $result["account"]["balance"]["total"]; // int
     * $result["account"]["balance"]["blocked"]; // int
     * $result["account"]["balance"]["available"]; // int
     * ```
     *
     * @link https://developers.openpix.com.br/api#tag/account/paths/~1api~1v1~1account~1%7BaccountId%7D/get
     *
     * @param string $accountId Account ID.
     *
     * @return array<string, mixed> Result from API.
     */
    public function getOne(string $accountId): array
    {
        $request = (new Request())
            ->method("GET")
            ->path("/api/v1/account/" . $accountId);

        return $this->requestTransport->transport($request);
    }

    /**
     * Withdraw from an Account via accountId param
     *
     * ```php
     * $result = $client->accounts()->withdraw('accountId', [
     *     "value" => 1000, // R$ 10,00
     * ]);
     *
     * $result["withdraw"]["account"]; // array
     * $result["withdraw"]["account"]["accountId"]; // string
     * $result["withdraw"]["account"]["isDefault"]; // boolean
     * $result["withdraw"]["account"]["balance"]; // array
     * $result["withdraw"]["account"]["balance"]["total"]; // int
     * $result["withdraw"]["account"]["balance"]["blocked"]; // int
     * $result["withdraw"]["account"]["balance"]["available"]; // int
     *
     * $result["withdraw"]["transaction"]; // array
     * $result["withdraw"]["transaction"]["endToEndId"]; // string
     * $result["withdraw"]["transaction"]["transaction"]; // int
     * ```
     *
     * @link https://developers.openpix.com.br/api#tag/account/paths/~1api~1v1~1account~1%7BaccountId%7D~1withdraw/post
     *
     * @param string $accountId Account ID.
     * @param array<string, mixed> $data amount data.
     *
     * @return array<string, mixed> Result from API.
     */
    public function withdraw(string $accountId, array $data): array
    {
        $request = (new Request())
            ->method("POST")
            ->path("/api/v1/account/".$accountId."/withdraw")
            ->body($data);

        return $this->requestTransport->transport($request);
    }
}
