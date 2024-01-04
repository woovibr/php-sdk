<?php

namespace OpenPix\PhpSdk\Resources;

use OpenPix\PhpSdk\Paginator;
use OpenPix\PhpSdk\Request;
use OpenPix\PhpSdk\RequestTransport;

/**
 * Operations on customers.
 *
 * @link https://developers.openpix.com.br/api#tag/customer
 */
class Customers
{
    /**
     * Used to send HTTP requests to customers API.
     *
     * @var RequestTransport
     */
    private $requestTransport;

    /**
     * Create a new Customers instance.
     *
     * @param RequestTransport $requestTransport Used to send HTTP requests to customers API.
     */
    public function __construct(RequestTransport $requestTransport)
    {
        $this->requestTransport = $requestTransport;
    }

    /**
     * List customers using an {@see Paginator}.
     *
     * ## Usage
     * ```php
     * $paginator = $client->customers()->list();
     *
     * foreach ($paginator as $result) {
     *      foreach ($result["customers"] as $customer) {
     *          $customer["name"]; // string
     *          $customer["taxID"]["type"]; // string
     *          $customer["taxID"]["taxID"]; // string
     *          // and more fields...
     *      }
     * }
     * ```
     *
     * @link https://developers.openpix.com.br/api#tag/customer/paths/~1api~1v1~1customer/get
     *
     * @return Paginator Paginator with results from API.
     */
    public function list(): Paginator
    {
        $request = (new Request())
            ->method("GET")
            ->path("/api/v1/customer");

        return new Paginator($this->requestTransport, $request);
    }

    /**
     * Get one customer by correlation ID.
     *
     * ```php
     * $result = $client->customers()->getOne("correlationID");
     *
     * $result["customer"]["name"]; // string
     * $result["customer"]["correlationID"]; // string
     * $result["customer"]["address"]["zipcode"]; // string
     * ```
     *
     * @link https://developers.openpix.com.br/api#tag/customer/paths/~1api~1v1~1customer~1%7Bid%7D/get
     *
     * @param string $correlationID Correlation ID.
     *
     * @return array<string, mixed> Result from API.
     */
    public function getOne(string $correlationID): array
    {
        $request = (new Request())
            ->method("GET")
            ->path("/api/v1/customer/" . $correlationID);

        return $this->requestTransport->transport($request);
    }

    /**
     * Create a new Customer.
     *
     * ```php
     * $result = $client->customers()->create([
     *      "name" => "", // Name of customer. Required. (string).
     *      "taxID" => "", // Tax ID of customer. Required. (string).
     *      "correlationID" => "", // Correlation ID. (string).
     *      // and more fields...
     * ]);
     *
     * $customer = $result["customer"];
     *
     * $customer["name"]; // string
     * $customer["correlationID"]; // string
     * ```
     *
     * @link https://developers.openpix.com.br/api#tag/customer/paths/~1api~1v1~1customer/post
     *
     * @param array<string, mixed> $data Customer data.
     *
     * @return array<string, mixed> Result from API.
     */
    public function create(array $data): array
    {
        $request = (new Request())
            ->method("POST")
            ->path("/api/v1/customer")
            ->body($data);

        return $this->requestTransport->transport($request);
    }
}
