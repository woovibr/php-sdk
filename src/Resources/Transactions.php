<?php

namespace OpenPix\PhpSdk\Resources;

use OpenPix\PhpSdk\Paginator;
use OpenPix\PhpSdk\Request;
use OpenPix\PhpSdk\RequestTransport;

/**
 * Operations on transactions.
 *
 * @link https://developers.openpix.com.br/api#tag/transactions
 */
class Transactions
{
    /**
     * Used to send HTTP requests to transactions API.
     *
     * @var RequestTransport
     */
    private $requestTransport;

    /**
     * Create a new Transactions instance.
     *
     * @param RequestTransport $requestTransport Used to send HTTP requests to transactions API.
     */
    public function __construct(RequestTransport $requestTransport)
    {
        $this->requestTransport = $requestTransport;
    }

    /**
     * Get one transaction by ID.
     *
     * ## Usage
     * ```php
     * $result = $client->transactions()->getOne("transactionID");
     *
     * $transaction = $result["transaction"]; // array
     * $transaction["value"]; // number
     * $transaction["charge"]; // Charge as array. {@see Charges} resource.
     * $transaction["customer"]; // Customer as array. {@see Customers} resource.
     * $transaction["withdraw"]; // PixWithdrawTransaction as array.
     *
     * // and more fields...
     * ```
     *
     * @link https://developers.openpix.com.br/api#tag/transactions/paths/~1api~1v1~1transaction~1%7Bid%7D/get
     *
     * @param string $transactionID You can use the transaction id from OpenPix
     * or the endToEndId of transaction from bank.
     *
     * @return array<string, mixed> Result from API.
     */
    public function getOne(string $transactionID): array
    {
        $request = (new Request())
            ->method("GET")
            ->path("/api/v1/transaction/" . $transactionID);

        return $this->requestTransport->transport($request);
    }

    /**
     * Return an {@see Paginator} with API transaction list results wrapped.
     *
     * ```php
     * $paginator = $client->transactions()->list([
     *      "start" => "", // Start date used in the query. Complies with RFC 3339. Optional.
     *      "end" => "", // End date used in the query. Complies with RFC 3339. Optional.
     *
     *      // You can use the charge ID or correlation ID or transaction ID of charge to
     *      // get a list of transactions related of this transaction. Optional.
     *      "charge" => "",
     *
     *      // You can use the QrCode static ID or correlation ID or identifier field
     *      // of QrCode static to get a list of QrCode related of this transaction.
     *      // Optional.
     *      "pixQrCode" => "",
     *
     *      // You can use the ID or EndToEndId of a withdrawal transaction to get all
     *      // transactions related to the withdrawal. Optional.
     *      "withdrawal" => "",
     * ]);
     *
     * foreach ($paginator as $result) {
     *      foreach ($result["transactions"] as $transaction) {
     *          $transaction; // array
     *          $transaction["value"]; // int
     *      }
     * }
     * ```
     *
     * @link https://developers.openpix.com.br/api#tag/transactions/paths/~1api~1v1~1transaction/get
     *
     * @param array<string, mixed> $queryParams
     *
     * @return Paginator Paginator with results from API.
     */
    public function list(array $queryParams): Paginator
    {
        $request = (new Request())
            ->method("GET")
            ->path("/api/v1/transaction")
            ->queryParams($queryParams);

        return new Paginator($this->requestTransport, $request);
    }
}
