<?php

namespace OpenPix\PhpSdk\Resources;

use OpenPix\PhpSdk\Paginator;
use OpenPix\PhpSdk\Request;
use OpenPix\PhpSdk\RequestTransport;

/**
 * Operations on refunds.
 */
class Refunds
{
    /**
     * Transport used to send HTTP requests.
     */
    private RequestTransport $requestTransport;

    /**
     * Create a new Refunds instance.
     *
     * @param RequestTransport Used to send HTTP requests for Refunds API.
     */
    public function __construct(RequestTransport $requestTransport)
    {
        $this->requestTransport = $requestTransport;
    }

    /**
     * Return an {@see Paginator} with API refund list results wrapped.
     *
     * ```php
     * $paginator = $client->refunds()->list();
     *
     * foreach ($paginator as $result) {
     *      foreach ($result["refunds"] as $refund) {
     *          $refund; // array
     *          $refund["value"]; // int
     *          $refund["correlationID"]; // string
     *
     *          // Enum: "IN_PROCESSING", "REFUNDED" or "NOT_ACCOMPLISHED"
     *          $refund["status"];
     *
     *          // Unique refund ID for this pix refund
     *          $refund["refundId"]; // string
     *
     *          // and more...
     *      }
     * }
     * ```
     *
     * @link https://developers.openpix.com.br/api#tag/refund/paths/~1api~1v1~1refund/get
     *
     * @param array<string, mixed> $queryParams
     *
     * @return Paginator
     */
    public function list(array $params = []): Paginator
    {
        $request = (new Request())
            ->method("GET")
            ->path("/refund")
            ->queryParams($queryParams);

        return new Paginator($this->requestTransport, $request);
    }

    /**
     * Get one refund by ID.
     *
     * ## Usage
     * ```php
     * $result = $client->refunds()->getOne("refundID");
     *
     * $result["refund"]; // array
     * $result["refund"]["value"]; // int
     * $result["refund"]["correlationID"]; // string
     *
     * // Enum: "IN_PROCESSING", "REFUNDED" or "NOT_ACCOMPLISHED"
     * $result["refund"]["status"];
     *
     * // Unique refund ID for this pix refund
     * $result["refund"]["refundId"]; // string
     *
     * // and more...
     * ```
     *
     * @link https://developers.openpix.com.br/api#tag/refund/paths/~1api~1v1~1refund~1%7Bid%7D/get
     *
     * @param string $refundID Refund ID or correlation ID.
     *
     * @return array<string, mixed> Result from API.
     */
    public function getOne(string $refundID): array
    {
        $request = (new Request())
            ->method("GET")
            ->path("/refund/" . $refundID);

        return $this->requestTransport->transport($request);
    }

    /**
     * Create a new refund.
     *
     * ## Usage
     * ```php
     * $result = $client->refunds()->create([
     *      "value" => 0,
     *      // Your transaction ID, or endToEnd ID, to keep track of this refund.
     *      "transactionEndToEndId" => "",
     *      // Your correlation ID, unique identifier refund
     *      "correlationID" => "",
     *      // Comment of this refund. Maximum length of 140 characters.
     *      "comment" => "",
     * ]);
     *
     * $result["refund"]; // array
     * $result["refund"]["value"]; // int
     * $result["refund"]["correlationID"]; // string
     *
     * // Enum: "IN_PROCESSING", "REFUNDED" or "NOT_ACCOMPLISHED"
     * $result["refund"]["status"];
     *
     * // Unique refund ID for this pix refund
     * $result["refund"]["refundId"]; // string
     *
     * // and more...
     * ```
     *
     * @link https://developers.openpix.com.br/api#tag/refund/paths/~1api~1v1~1refund/post
     *
     * @param array<string, mixed> $refund Refund data.
     *
     * @return array<string, mixed> Result from API.
     */
    public function create(array $refund): array
    {
        $request = (new Request())
            ->method("POST")
            ->path("/refund")
            ->body($refund);

        return $this->requestTransport->transport($request);
    }
}
