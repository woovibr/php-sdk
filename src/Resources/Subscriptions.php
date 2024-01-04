<?php

namespace OpenPix\PhpSdk\Resources;

use OpenPix\PhpSdk\Request;
use OpenPix\PhpSdk\RequestTransport;

/**
 * Operations on subscriptions.
 */
class Subscriptions
{
    /**
     * Used to send HTTP requests.
     *
     * @var RequestTransport
     */
    private $requestTransport;

    /**
     * Create a new Subscriptions instance.
     *
     * @param RequestTransport $requestTransport Used to send HTTP requests.
     */
    public function __construct(RequestTransport $requestTransport)
    {
        $this->requestTransport = $requestTransport;
    }

    /**
     * Get one subscription.
     *
     * ## Usage
     * ```php
     * $result = $client->subscriptions()->getOne("subscription id");
     *
     * $result["subscription"]; // Subscription array
     *
     * // The globalID of the subscription.
     * $result["subscription"]["globalID"];
     *
     * // Value in cents of the subscription.
     * $result["subscription"]["value"];
     *
     * // Day of the month that the charges will be generated
     * $result["subscription"]["dayGenerateCharge"];
     *
     * // Customer of the subscription. {@see Customer} resource.
     * $result["subscription"]["customer"];
     * $result["subscription"]["customer"]["taxID"]["taxID"];
     * ```
     *
     * @link https://developers.openpix.com.br/api#tag/subscription/paths/~1api~1v1~1subscriptions~1%7Bid%7D/get
     *
     * @param string $subscriptionID
     *
     * @return array<string, mixed>
     */
    public function getOne(string $subscriptionID): array
    {
        $request = (new Request())
            ->method("GET")
            ->path("/api/v1/subscriptions/" . $subscriptionID);

        return $this->requestTransport->transport($request);
    }

    /**
     * Create a new subscription.
     *
     * ## Usage
     * ```php
     * $result = $client->subscriptions()->create([
     *      "value" => 100, // Value in cents of this subscription. Required.
     *      // Customer of subscription. Required.
     *      "customer" => [
     *          "name" => "Dan",
     *          "taxID" => "31324227036", // Customer taxID (CPF or CNPJ)
     *          "email" => "email0@example.com",
     *          "phone" => "5511999999999",
     *      ],
     *      // Day of the month that the charges will be generated. Maximum of 27. Default 5.
     *      "dayGenerateCharge" => 15,
     * ]);
     *
     * // The globalID of the subscription.
     * $result["subscription"]["globalID"];
     *
     * // Value in cents of the subscription.
     * $result["subscription"]["value"];
     *
     * // Day of the month that the charges will be generated
     * $result["subscription"]["dayGenerateCharge"];
     *
     * // Customer of the subscription.
     * $result["subscription"]["customer"];
     * $result["subscription"]["customer"]["taxID"]["taxID"];
     * ```
     *
     * @link https://developers.openpix.com.br/api#tag/subscription/paths/~1api~1v1~1subscriptions/post
     *
     * @param array<string, mixed> $subscription
     *
     * @return array<string, mixed>
     */
    public function create(array $subscription): array
    {
        $request = (new Request())
            ->method("POST")
            ->path("/api/v1/subscriptions")
            ->body($subscription);

        return $this->requestTransport->transport($request);
    }
}
