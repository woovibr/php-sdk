<?php

namespace OpenPix\PhpSdk\Resources;

use OpenPix\PhpSdk\Paginator;
use OpenPix\PhpSdk\Request;
use OpenPix\PhpSdk\RequestTransport;

/**
 * Operations on partners. To use this section of the SDK you need to request access to OpenPix.
 *
 * @link https://developers.openpix.com.br/api#tag/partner-(request-access)
 */
class Partners
{
    /**
     * Used to send HTTP requests to partners API.
     *
     * @var RequestTransport
     */
    private $requestTransport;

    /**
     * Create a new Partners instance.
     *
     * @param RequestTransport $requestTransport Used to send HTTP requests to partners API.
     */
    public function __construct(RequestTransport $requestTransport)
    {
        $this->requestTransport = $requestTransport;
    }

    /**
     * Get every preregistration that is managed by you. {@see Paginator}.
     *
     * ## Usage
     * ```php
     * $paginator = $client->partners()->list();
     *
     * foreach ($paginator as $result) {
     *      foreach ($result["preRegistrations"] as $partner) {
     *          $partner["preRegistration"]; // array
     *          $partner["preRegistration"]["name"]; // string
     *          $partner["user"]; // array
     *          $partner["user"]["firstName"]; // string
     *          $partner["user"]["email"]; // string
     *          $partner["company"]["name"]; // string
     *          // and more fields...
     *      }
     * }
     * ```
     *
     * @link https://developers.openpix.com.br/api#tag/partner-(request-access)/paths/~1api~1v1~1partner~1company/get
     *
     * @return Paginator with results from API.
     */
    public function list(): Paginator
    {
        $request = (new Request())
            ->method("GET")
            ->path("/api/v1/partner/company");

        return new Paginator($this->requestTransport, $request);
    }

    /**
     * Get a specific preregistration via taxID param.
     *
     * ```php
     * $result = $client->partners()->getOne("taxID");
     *
     * $result["preRegistration"]["preRegistration"]["name"]; // string
     * $result["preRegistration"]["user"]["firstName"]; // string
     * $result["preRegistration"]["company"]["name"]; // string
     * $result["preRegistration"]["account"]["clientId"]; // string
     * // and more fields...
     * ```
     *
     * @link https://developers.openpix.com.br/api#tag/partner-(request-access)/paths/~1api~1v1~1partner~1company~1%7BtaxID%7D/get
     *
     * @param string $taxID Tax ID.
     *
     * @return array<string, mixed> Result from API.
     */
    public function getOne(string $taxID): array
    {
        $request = (new Request())
            ->method("GET")
            ->path("/api/v1/partner/company/" . $taxID);

        return $this->requestTransport->transport($request);
    }

    /**
     * Create a preregistration with a partner reference (your company)
     *
     * ```php
     * $result = $client->partners()->create([
     *      "preRegistration" => [
     *         "name" => "Example LLC", // Name of partner company. Required. (string).
     *         "website" => "", // Website of the partner company. (string).
     *         "taxID" => [
     *           "taxID" => "11111111111111" // Tax ID of the partner company. Required. (string).
     *           "type" => "BR:CNPJ" // Type of the Tax ID of the partner company. Required. (string).
     *         ],
     *      ],
     *      "user" => [
     *        "firstName" => "John", // First Name of the partner. Required. (string).
     *        "lastName" => "Doe", // Last Name of the partner. Required. (string).
     *        "email" => "johndoe@examplellc.com", // E-mail of the partner. Required. (string).
     *        "phone" => "+5511912345678", // Phone number of the partner. Required. (string).
     *      ],
     *      // and more fields...
     * ]);
     *
     * $result["preRegistration"]["name"]; // string
     * $result["preRegistration"]["website"]; // string
     * $result["preRegistration"]["taxID"]["taxID"]; // string
     * $result["preRegistration"]["taxID"]["type"]; // string
     *
     * $result["user"]["firstName"]; // string
     * $result["user"]["lastName"]; // string
     * $result["user"]["email"]; // string
     * $result["user"]["phone"]; // string
     * ```
     *
     * @link https://developers.openpix.com.br/api#tag/partner-(request-access)/paths/~1api~1v1~1partner~1company/post
     *
     * @param array<string, mixed> $data Partner data.
     *
     * @return array<string, mixed> Result from API.
     */
    public function create(array $data): array
    {
        $request = (new Request())
            ->method("POST")
            ->path("/api/v1/partner/company")
            ->body($data);

        return $this->requestTransport->transport($request);
    }

    /**
     * Create a new application to some of your preregistration's company.
     *
     * ```php
     * $result = $client->partners()->createApp([
     *      "application" => [
     *         "name" => "MyAPIAccess", // Name of app. Required. (string).
     *         "type" => "API", // Type of app. Enum: "API", "PLUGIN", "ORACLE". Required. (string).
     *      ],
     *      "taxID" => [
     *        "taxID" => "65914571000187", // Tax ID of the partner company. (string).
     *        "type" => "BR:CNPJ",
     *      ],
     *      // and more fields...
     * ]);
     *
     * $app = $result["application"];
     *
     * $app["name"]; // string
     * $app["isActive"]; // boolean
     * $app["type"]; // string
     * $app["clientId"]; // string
     * $app["clientSecret"]; // string
     * ```
     *
     * @link https://developers.openpix.com.br/api#tag/partner-(request-access)/paths/~1api~1v1~1partner~1application/post
     *
     * @param array<string, mixed> $data Partner data.
     *
     * @return array<string, mixed> Result from API.
     */
    public function createApp(array $data): array
    {
        $request = (new Request())
            ->method("POST")
            ->path("/api/v1/partner/application")
            ->body($data);

        return $this->requestTransport->transport($request);
    }
}
