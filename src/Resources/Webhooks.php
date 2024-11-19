<?php

namespace OpenPix\PhpSdk\Resources;

use OpenPix\PhpSdk\Paginator;
use OpenPix\PhpSdk\Request;
use OpenPix\PhpSdk\RequestTransport;

/**
 * Operations on webhooks.
 */
class Webhooks
{
    /**
     * Used for webhook signature validation.
     */
    private const VALIDATION_PUBLIC_KEY_BASE64 = "LS0tLS1CRUdJTiBQVUJMSUMgS0VZLS0tLS0KTUlHZk1BMEdDU3FHU0liM0RRRUJBUVVBQTRHTkFEQ0JpUUtCZ1FDLytOdElranpldnZxRCtJM01NdjNiTFhEdApwdnhCalk0QnNSclNkY2EzcnRBd01jUllZdnhTbmQ3amFnVkxwY3RNaU94UU84aWVVQ0tMU1dIcHNNQWpPL3paCldNS2Jxb0c4TU5waS91M2ZwNnp6MG1jSENPU3FZc1BVVUcxOWJ1VzhiaXM1WloySVpnQk9iV1NwVHZKMGNuajYKSEtCQUE4MkpsbitsR3dTMU13SURBUUFCCi0tLS0tRU5EIFBVQkxJQyBLRVktLS0tLQo=";

    /**
     * Request transport used to send HTTP requests.
     *
     * @var RequestTransport
     */
    private $requestTransport;

    /**
     * Create a new Webhooks instance.
     *
     * @param RequestTransport $requestTransport Request transport used to send HTTP requests.
     */
    public function __construct(RequestTransport $requestTransport)
    {
        $this->requestTransport = $requestTransport;
    }

    /**
     * List registered webhooks using {@see Paginator}.
     *
     * ## Usage
     * ```php
     * $paginator = $client->webhooks()->list();
     *
     * foreach ($paginator as $result) {
     *      foreach ($result["webhooks"] as $webhook) {
     *          $webhook["name"]; // string
     *          $webhook["event"]; // string
     *          $webhook["url"]; // string
     *          $webhook["isActive"]; // bool
     *          // and more...
     *      }
     * }
     * ```
     *
     * @link https://developers.openpix.com.br/api#tag/webhook/paths/~1api~1v1~1webhook/get
     *
     * @param string $filterByUrl You can use the url to filter all webhooks.
     *
     * @return Paginator
     */
    public function list(string $filterByUrl = ""): Paginator
    {
        $request = (new Request())
            ->method("GET")
            ->path("/api/v1/webhook")
            ->queryParams(["url" => $filterByUrl]);

        return new Paginator($this->requestTransport, $request);
    }

    /**
     * Delete a webhook by ID.
     *
     * ## Usage
     * ```php
     * $client->webhooks()->delete($webhookID);
     * ```
     *
     * @link https://developers.openpix.com.br/api#tag/webhook/paths/~1api~1v1~1webhook~1%7Bid%7D/delete
     *
     * @return array<string, mixed>
     */
    public function delete(string $webhookID): array
    {
        $request = (new Request())
            ->method("DELETE")
            ->path("/api/v1/webhook/" . $webhookID);

        return $this->requestTransport->transport($request);
    }

    /**
     * Create a new webhook.
     *
     * ## Usage
     * ```php
     * $result = $client->webhooks()->create([
     *      "webhook" => [
     *          "name" => "", // string
     *          // Available events to register a webhook to listen to. If no one selected anyone the
     *          // default event will be OPENPIX:TRANSACTION_RECEIVED.
     *          // OPENPIX:CHARGE_CREATED - New charge created
     *          // OPENPIX:CHARGE_COMPLETED - Charge completed is when a charge is fully paid
     *          // OPENPIX:CHARGE_EXPIRED - Charge expired is when a charge is not fully paid and expired
     *          // OPENPIX:TRANSACTION_RECEIVED - New PIX transaction received
     *          // OPENPIX:TRANSACTION_REFUND_RECEIVED - New PIX transaction refund received or refunded
     *          // OPENPIX:MOVEMENT_CONFIRMED - Payment confirmed is when the pix transaction related to the payment gets confirmed
     *          // OPENPIX:MOVEMENT_FAILED - Payment failed is when the payment gets approved and a error occurs
     *          // OPENPIX:MOVEMENT_REMOVED - Payment was removed by a user
     *          "event" => "", // string
     *          "url" => "", // string
     *          "authorization" => "", // string
     *          "isActive" => false, // bool
     *      ]
     * ]);
     *
     * $result["webhook"]["id"]; // string
     * $result["webhook"]["name"]; // string
     * // Available events to register a webhook to listen to.
     * // If no one selected anyone the default event will be "OPENPIX:TRANSACTION_RECEIVED".
     * $result["webhook"]["event"]; // string
     * $result["webhook"]["url"]; // string
     * $result["webhook"]["authorization"]; // string
     * $result["webhook"]["isActive"]; // bool
     * $result["webhook"]["createdAt"]; // string
     * $result["webhook"]["updatedAt"]; // string
     * ```
     *
     * @link https://developers.openpix.com.br/api#tag/webhook/paths/~1api~1v1~1webhook/post
     *
     * @param array<string, mixed> $webhook
     *
     * @return array<string, mixed>
     */
    public function create(array $webhook): array
    {
        $request = (new Request())
            ->method("POST")
            ->path("/api/v1/webhook")
            ->body($webhook);

        return $this->requestTransport->transport($request);
    }

    /**
     * Get a list of webhook IPs.
     *
     * ## Usage
     * ```php
     * $result = $client->webhooks()->ips();
     *
     * foreach ($result["ips"] as $ip) {
     *     echo $ip; // string
     * }
     *
     * $result["ips"][0]; // "189.51.60.9"
     * $result["ips"][1]; // "138.97.124.129"
     * $result["ips"][2]; // "177.71.136.66"
     * ```
     *
     * @link https://developers.openpix.com.br/api#tag/webhook/paths/~1api~1v1~1webhook~1ips/get
     *
     * @return array<string, mixed>
     */
    public function ips(): array
    {
        $request = (new Request())
            ->method("GET")
            ->path("/api/v1/webhook/ips");

        return $this->requestTransport->transport($request);
    }

    /**
     * Validate webhook signature.
     *
     * Every Webhook request has the header `x-webhook-signature` which is the signature
     * generated with Woovi's secret key and the Webhook payload. Upon receiving the
     * header, you can validate whether the signature is valid using this method and
     * continue the Webhook flow.
     *
     * ## Usage
     * ```php
     * $payload = file_get_contents("php://input");
     * $signature = getallheaders()["x-webhook-signature"];
     *
     * $client->webhooks()->isWebhookValid($payload, $signature);
     * ```
     *
     * @link https://developers.openpix.com.br/docs/webhook/webhook-signature-validation
     *
     * @param string $payload Webhook payload string encoded in JSON. You can get this using `file_get_contents("php://input")`, for example.
     * @param string $signature `x-webhook-signature` HTTP header sent by OpenPix request. You can get this using the `getallheaders` function, for example.
     *
     * @return bool Returns `true` if the received webhook is valid and `false` otherwise.
     */
    public function isWebhookValid(string $payload, string $signature): bool
    {
        return openssl_verify(
            $payload,
            base64_decode($signature),
            base64_decode(self::VALIDATION_PUBLIC_KEY_BASE64),
            "sha256WithRSAEncryption"
        ) === 1;
    }
}
