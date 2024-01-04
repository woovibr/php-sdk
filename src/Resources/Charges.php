<?php

namespace OpenPix\PhpSdk\Resources;

use OpenPix\PhpSdk\Paginator;
use OpenPix\PhpSdk\Request;
use OpenPix\PhpSdk\RequestTransport;

/**
 * Operations on charges.
 */
class Charges
{
    /**
     * The transport used by {@see Request}.
     *
     * @var RequestTransport
     */
    private $requestTransport;

    /**
     * Create a new Charges instance.
     *
     * @param RequestTransport $requestTransport Used to send HTTP requests.
     */
    public function __construct(RequestTransport $requestTransport)
    {
        $this->requestTransport = $requestTransport;
    }

    /**
     * Return an {@see Paginator} with charge list results wrapped.
     *
     * ## Usage
     * ```php
     * $params = [
     *      // Start date. Complies with RFC 3339. Optional. (?string)
     *      "start" => "",
     *      // End date. Complies with RFC 3339. Optional. (?string)
     *      "end" => "",
     *      // "COMPLETED", "EXPIRED" or "EXPIRED". Optional. (?string)
     *      "status" => "",
     * ];
     *
     * foreach ($client->charges()->list($params) as $result) {
     *     foreach ($result["charges"] as $charge) {
     *          $charge["type"];
     *          $charge["correlationID"];
     *          $charge["paymentLinkID"]; // Payment Link ID, used on payment link and to retrieve qrcode image
     *          $charge["paymentLinkUrl"]; // Payment Link URL to be shared with customers
     *          // ...
     *     }
     * }
     * ```
     *
     * @link https://developers.openpix.com.br/api#tag/charge/paths/~1api~1v1~1charge/get
     *
     * @param array<string, mixed> $params Query parameters.
     *
     * @return Paginator Paginator with results wrapped.
     */
    public function list(array $params = []): Paginator
    {
        $request = (new Request())
            ->method("GET")
            ->path("/api/v1/charge")
            ->queryParams($params);

        return new Paginator($this->requestTransport, $request);
    }

    /**
     * Get one charge by ID.
     *
     * ## Usage
     * ```php
     * $result = $client->charges()->getOne($id);
     *
     * $result["charge"]; // array
     * // (string) Charge type is used to determine whether a charge will have a deadline, fines and interests.
     * // Can be "DYNAMIC" or "OVERDUE"
     * $result["charge"]["type"];
     * ```
     *
     * @link https://developers.openpix.com.br/api#tag/charge/paths/~1api~1v1~1charge~1%7Bid%7D/get
     *
     * @param string $id charge ID or correlation ID.
     * You will need URI encoding if your correlation ID has characters outside
     * the ASCII set or reserved characters (%, #, /).
     *
     * @return array<string, mixed> Result from API.
     */
    public function getOne(string $id): array
    {
        $request = (new Request())
            ->method("GET")
            ->path("/api/v1/charge/" . $id);

        return $this->requestTransport->transport($request);
    }

    /**
     * Create a charge.
     *
     * ## Usage
     * ```php
     * // Creating a charge of value $10000.
     * $result = $client->charges()->create([
     *      "value" => 10000,
     *      "correlationID" => "Correlation ID here",
     * ]);
     *
     * $result["charge"]; // An array with charge data
     * $result["brCode"]; // string
     * $result["correlationID"]; // string
     * ```
     *
     * @link https://developers.openpix.com.br/api#tag/charge/paths/~1api~1v1~1charge/get
     *
     * @param array<string, mixed> $charge Charge data.
     * @param bool $returnExisting Make the endpoint idempotent, will return
     * an existent charge if already has a one with the correlationID.
     *
     * @return array<string, mixed> Result from API.
     */
    public function create(array $charge, bool $returnExisting = true): array
    {
        $request = (new Request())
            ->method("POST")
            ->path("/api/v1/charge")
            ->body($charge)
            ->queryParams(["return_existing" => $returnExisting]);

        return $this->requestTransport->transport($request);
    }

    /**
     * Delete a charge by ID.
     *
     * ## Usage
     * ```php
     * $result = $client->charges()->delete($chargeId);
     *
     * $result["status"]; // string
     * $result["id"]; // the id previously informed to be found and deleted
     * ```
     *
     * @param string $id Charge ID or correlation ID. You will need URI encoding if your
     * correlation ID has characters outside the ASCII set or reserved characters (%, #, /).
     *
     * @return array<string, mixed> Result from API.
     */
    public function delete(string $id): array
    {
        $request = (new Request())
            ->method("DELETE")
            ->path("/api/v1/charge/" . $id);

        return $this->requestTransport->transport($request);
    }

    /**
     * Get QR code image link by a paymentLinkID.
     *
     * @link https://developers.openpix.com.br/api#tag/charge/paths/~1openpix~1charge~1brcode~1image~1%7B:id%7D.png?size=1024/get
     *
     * @param string $paymentLinkID Charge link payment ID.
     * @param int $size Size for the image. This size should be between 600 and 4096.
     * If the size parameter was not passed, the default value will be 1024.
     *
     * @return string The URL of image.
     */
    public function getQrCodeImageLink(string $paymentLinkID, int $size = 1024): string
    {
        return $this->requestTransport->getBaseUri() . "/openpix/charge/brcode/image/" . $paymentLinkID . ".png?size=" . $size;
    }
}
