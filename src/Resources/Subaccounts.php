<?php

namespace OpenPix\PhpSdk\Resources;

use OpenPix\PhpSdk\Paginator;
use OpenPix\PhpSdk\Request;
use OpenPix\PhpSdk\RequestTransport;

/**
 * Operations on subaccounts.
 *
 * @link https://developers.woovi.com/api#tag/subaccount
 */
class Subaccounts
{
    /**
     * Used to send HTTP requests to subaccounts API.
     *
     * @var RequestTransport
     */
    private $requestTransport;

    /**
     * Create a new Subaccounts instance.
     *
     * @param RequestTransport $requestTransport Used to send HTTP requests to subaccounts API.
     */
    public function __construct(RequestTransport $requestTransport)
    {
        $this->requestTransport = $requestTransport;
    }

    /**
     * Return an {@see Paginator} with subaccount list.
     *
     * ## Usage
     * ```php
     * $paginator = $client->subaccounts()->list();
     *
     * foreach ($paginator as $page) {
     *     foreach ($page["subAccounts"] as $subAccount) {
     *         $subAccount["name"]; // string
     *         $subAccount["pixKey"]; // string
     *         $subAccount["balance"]; // int
     *     }
     * }
     * ```
     *
     * @link https://developers.woovi.com/api#tag/subaccount/GET/api/v1/subaccount
     *
     * @param array<string, mixed> $queryParams Query parameters.
     *
     * @return Paginator Paginated result from API.
     */
    public function list(array $queryParams = []): Paginator
    {
        $request = (new Request())
            ->method("GET")
            ->path("/api/v1/subaccount")
            ->queryParams($queryParams);

        return new Paginator($this->requestTransport, $request);
    }

    /**
     * Get a subaccount via pix key.
     *
     * ```php
     * $result = $client->subaccounts()->getOne("pixKey");
     *
     * $result["subAccount"]["name"]; // string
     * $result["subAccount"]["pixKey"]; // string
     * $result["subAccount"]["balance"]; // int
     * $result["subAccount"]["withdrawBlocked"]; // bool|null
     * ```
     *
     * @link https://developers.woovi.com/api#tag/subaccount/GET/api/v1/subaccount/{id}
     *
     * @param string $id Pix key registered to the subaccount.
     *
     * @return array<string, mixed> Result from API.
     */
    public function getOne(string $id): array
    {
        $request = (new Request())
            ->method("GET")
            ->path("/api/v1/subaccount/" . $id);

        return $this->requestTransport->transport($request);
    }

    /**
     * Withdraw from a sub account and return the withdrawal transaction information.
     *
     * ```php
     * $result = $client->subaccounts()->withdraw("subaccountId", [
     *     "value" => 1000, // R$ 10,00
     * ]);
     *
     * $result["transaction"]["status"]; // string. e.g.: CREATED
     * $result["transaction"]["value"]; // int
     * $result["transaction"]["endToEndId"]; // string
     * $result["transaction"]["correlationID"]; // string
     * $result["transaction"]["destinationAlias"]; // string
     * $result["transaction"]["comment"]; // string
     * ```
     *
     * @link https://developers.woovi.com/api#tag/subaccount/POST/api/v1/subaccount/{id}/withdraw
     *
     * @param string $id Pix key registered to the subaccount.
     * @param array<string, mixed> $data Data to make a withdraw partial. Omit `value` to withdraw the full balance.
     *
     * @return array<string, mixed> Result from API.
     */
    public function withdraw(string $id, array $data = []): array
    {
        $request = (new Request())
            ->method("POST")
            ->path("/api/v1/subaccount/" . $id . "/withdraw")
            ->body($data);

        return $this->requestTransport->transport($request);
    }

    /**
     * Create a sub account.
     *
     * ```php
     * $result = $client->subaccounts()->create([
     *     "name" => "Name of the sub account",
     *     "pixKey" => "The pix key of the sub account",
     * ]);
     * // Name of the sub account
     * $result["subAccount"]["name"]; // string
     * // The pix key for the sub account
     * $result["subAccount"]["pixKey"]; // string
     * ```
     *
     * @link https://developers.woovi.com/api#tag/subaccount/POST/api/v1/subaccount
     *
     * @param array<string, mixed> $data Data to create a new subAccount or retrieve existing one.
     *
     * @return array<string, mixed> The Subccount created or retrieved if exists using the given pix key.
     */
    public function create(array $data): array
    {
        $request = (new Request())
            ->method("POST")
            ->path("/api/v1/subaccount")
            ->body($data);

        return $this->requestTransport->transport($request);
    }

    /**
     * Delete a Sub Account​ if it has no remaining balance.
     *
     * ```php
     * $result = $client->subaccounts()->delete("pixKey");
     *
     * $result["pixKey"]; // string
     * $result["status"]; // string. e.g.: OK
     * ```
     *
     * @link https://developers.woovi.com/api#tag/subaccount/DELETE/api/v1/subaccount/{id}
     *
     * @param string $id Pix key registered to the subaccount
     *
     * @return array<string, mixed> Sub Account successfully deleted.
     */
    public function delete(string $id): array
    {
        $request = (new Request())
            ->method("DELETE")
            ->path("/api/v1/subaccount/" . $id);

        return $this->requestTransport->transport($request);
    }

    /**
     * Debit from a Sub Account and send to the main account​.
     *
     * Transfers the amount from the subaccount to the main account.
     *
     * ```php
     * $result = $client->subaccounts()->debitToMainAccount("sourceSubAccountPixKey", [
     *     "value" => 1000, // R$ 10,00
     *     // Optional description for the debit operation
     *     "description" => "Optional description",
     * ]);
     *
     * $result["pixKey"]; // string.
     * $result["description"]; // string.
     * $result["success"]; // string.
     * $result["value"]; // number.
     * ```
     *
     * @link https://developers.woovi.com/api#tag/subaccount/POST/api/v1/subaccount/{id}/debit
     *
     * @param string $id Pix key registered to the subaccount.
     * @param array<string, mixed> $data Data to make a debit from sub account to main account.
     *
     * @return array<string, mixed> Result from API.
     */
    public function debitToMainAccount(string $id, array $data): array
    {
        $request = (new Request())
            ->method("POST")
            ->path("/api/v1/subaccount/" . $id . "/debit")
            ->body($data);

        return $this->requestTransport->transport($request);
    }

    /**
     * Transfer between subaccounts​.
     *
     * ```php
     * $result = $client->subaccounts()->transferBetweenSubaccounts([
     *      "fromPixKey" => "3143da48-2bc7-49a4-89bd-4e22f73bfb0c", // string
     *      // Types: CPF, CNPJ, EMAIL, PHONE and RANDOM.
     *      "fromPixKeyType" => "RANDOM", // string
     *
     *      "toPixKey" => "c4249323-b4ca-43f2-8139-874baab09b93", // string
     *      "toPixKeyType" => "RANDOM", // string
     *
     *      "value" => 1000, // int
     *      "correlationID" => "correlation-id", // string
     * ]);
     *
     * $result["value"]; // int.
     * $result["destinationSubaccount"]["name"]; // string.
     * $result["destinationSubaccount"]["pixKey"]; // string.
     * $result["destinationSubaccount"]["balance"]; // int.
     * $result["originSubaccount"]["name"]; // string.
     * $result["originSubaccount"]["pixKey"]; // string.
     * $result["originSubaccount"]["balance"]; // int.
     * ```
     *
     * @link https://developers.woovi.com/api#tag/subaccount/POST/api/v1/subaccount/transfer
     *
     * @param array<string, mixed> $data Data to make a new transfer between subaccounts
     *
     * @return array<string, mixed> Result from API.
     */
    public function transferBetweenSubaccounts(array $data): array
    {
        $request = (new Request())
            ->method("POST")
            ->path("/api/v1/subaccount/transfer")
            ->body($data);

        return $this->requestTransport->transport($request);
    }
}
