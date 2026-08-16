<?php

namespace OpenPix\PhpSdk;

use TypeError;
use Http\Discovery\Psr17FactoryDiscovery;
use Http\Discovery\Psr18ClientDiscovery;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;

/**
 * Transports HTTP requests to the API, adding data such as the Authorization and
 * User-Agent header, API path (like /api/v1) and handling errors and decoding responses.
 */
class RequestTransport
{
    /**
     * User Agent.
     */
    public const USER_AGENT = "openpix-php-sdk";

    /**
     * Underlying HTTP client.
     *
     * @var ClientInterface
     */
    private $httpClient;

    /**
     * Request factory passed to API `Request` builder.
     *
     * @var RequestFactoryInterface
     */
    private $requestFactory;

    /**
     * Stream factory passed to API `Request` builder.
     *
     * @var StreamFactoryInterface
     */
    private $streamFactory;

    /**
     * Application ID.
     *
     * @var string
     */
    private $appId;

    /**
     * Base URI of all requests handled by RequestTransport.
     *
     * @var string
     */
    private $baseUri;

    /**
     * Create a new RequestTransport instance.
     *
     * @link https://developers.openpix.com.br/docs/apis/api-getting-started
     *
     * @param string $appId Application ID.
     * @param string $baseUri Base URI of all requests handled by RequestTransport.
     * @param ?ClientInterface $httpClient PSR-18 HTTP Client. Is automatically
     * discovered with HTTPlug.
     * @param ?RequestFactoryInterface $requestFactory PSR-17 RequestFactory. It is automatically discovered with HTTPlug.
     * @param ?StreamFactoryInterface $streamFactory PSR-17 StreamFactory. It is automatically discovered with HTTPlug.
     */
    public function __construct(
        string $appId,
        string $baseUri,
        ?ClientInterface $httpClient = null,
        ?RequestFactoryInterface $requestFactory = null,
        ?StreamFactoryInterface $streamFactory = null
    ) {
        $this->appId = $appId;
        $this->baseUri = $baseUri;
        $this->httpClient = $httpClient ?? Psr18ClientDiscovery::find();
        $this->requestFactory = $requestFactory ?? Psr17FactoryDiscovery::findRequestFactory();
        $this->streamFactory = $streamFactory ?? Psr17FactoryDiscovery::findStreamFactory();
    }

    /**
     * Send the request to API.
     *
     * @param Request|RequestInterface $request
     *
     * @return array<string, mixed> Decoded response data.
     */
    public function transport($request): array
    {
        if (! ($request instanceof RequestInterface)) {
            $request = $request->build($this->baseUri, $this->requestFactory, $this->streamFactory);
        }

        $request = $this->withRequestDefaultParameters($request);

        $response = $this->httpClient->sendRequest($request);

        return $this->hydrateResponse($response);
    }

    /**
     * Get base URI of API.
     */
    public function getBaseUri(): string
    {
        return $this->baseUri;
    }

    /**
     * Add default headers like Authorization and `platform`.
     */
    private function withRequestDefaultParameters(RequestInterface $request): RequestInterface
    {
        return $request->withAddedHeader("User-Agent", self::USER_AGENT)
            ->withAddedHeader("Authorization", $this->appId)
            ->withAddedHeader("version", Client::SDK_VERSION)
            ->withAddedHeader("platform", "openpix-php-sdk");
    }

    /**
     * Decode response data.
     *
     * @return array<string, mixed>
     */
    private function hydrateResponse(ResponseInterface $response): array
    {
        $contents = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        if (!is_array($contents)) {
            throw new TypeError("Invalid response from API.");
        }

        if (!empty($contents["error"])) {
            $error = $contents["error"];

            if (is_array($error)) {
                $error = $error["message"] ?? $error["description"];
            }

            throw new ApiErrorException($error);
        }

        return $contents;
    }
}
