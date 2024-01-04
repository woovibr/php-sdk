<?php

namespace OpenPix\PhpSdk;

use OpenPix\PhpSdk\RequestTransport;
use OpenPix\PhpSdk\Resources\Charges;
use OpenPix\PhpSdk\Resources\Customers;
use OpenPix\PhpSdk\Resources\Transactions;
use OpenPix\PhpSdk\Resources\Subscriptions;
use OpenPix\PhpSdk\Resources\Webhooks;
use OpenPix\PhpSdk\Resources\Payments;
use OpenPix\PhpSdk\Resources\Refunds;

/**
 * The client provides a list of "resources", objects that allow it to send requests to
 * the API endpoints, returned by method calls in this class.
 */
class Client
{
    // {x-release-please-start-version}
    public const SDK_VERSION = "1.1.3";
    // {x-release-please-end}

    public const BASE_URI = "https://api.openpix.com.br";

    /**
     * @var RequestTransport
     */
    private $requestTransport;

    /**
     * Create a new client from an application ID and base URI (by default is OpenPix).
     *
     * @link https://developers.openpix.com.br/docs/apis/api-getting-started
     */
    public static function create(string $appId, string $baseUri = self::BASE_URI): self
    {
        return new self(new RequestTransport($appId, $baseUri));
    }

    /**
     * Create a new Client instance.
     *
     * @param RequestTransport $requestTransport Used by resources to transport requests to
     * endpoints.
     */
    public function __construct(RequestTransport $requestTransport)
    {
        $this->requestTransport = $requestTransport;
    }

    /**
     * Gets a `RequestTransport` that allows transporting custom requests using `Request`
     * objects.
     */
    public function getRequestTransport(): RequestTransport
    {
        return $this->requestTransport;
    }

    /**
     * Returns operations for the `Customer` resource.
     */
    public function customers(): Customers
    {
        return new Customers($this->requestTransport);
    }

    /**
     * Returns operations for the `Charge` resource.
     */
    public function charges(): Charges
    {
        return new Charges($this->requestTransport);
    }

    /**
     * Returns operations for the `Refund` resource.
     */
    public function refunds(): Refunds
    {
        return new Refunds($this->requestTransport);
    }

    /**
     * Returns operations for the `Refund` resource.
     */
    public function transactions(): Transactions
    {
        return new Transactions($this->requestTransport);
    }

    /**
     * Returns operations for the `Subscription` resource.
     */
    public function subscriptions(): Subscriptions
    {
        return new Subscriptions($this->requestTransport);
    }

    /**
     * Returns operations for the `Payment` resource.
     */
    public function payments(): Payments
    {
        return new Payments($this->requestTransport);
    }

    /**
     * Returns operations for the `Webhook` resource.
     */
    public function webhooks(): Webhooks
    {
        return new Webhooks($this->requestTransport);
    }
}
