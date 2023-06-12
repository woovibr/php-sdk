<?php

namespace OpenPix\PhpSdk;

use Http\Discovery\Psr17FactoryDiscovery;
use Http\Discovery\Psr18ClientDiscovery;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;

/**
 * Transports HTTP requests to the API, adding data such as the Authorization and
 * User-Agent header, handling errors and decoding responses.
 */
class RequestTransport
{
    public const USER_AGENT = "openpix-php-sdk";

    private ClientInterface $httpClient;

    private RequestFactoryInterface $requestFactory;

    private StreamFactoryInterface $streamFactory;

    private string $appId;

    private string $baseUri;

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
     * @return array Decoded response data
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

    private function withRequestDefaultParameters(RequestInterface $request): RequestInterface
    {
        return $request->withAddedHeader("User-Agent", self::USER_AGENT)
            ->withAddedHeader("Authorization", $this->appId);
    }

    private function hydrateResponse(ResponseInterface $response): array
    {
        $contents = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        if (!empty($contents["error"])) {
            throw new ApiErrorException($contents["error"]);
        }

        return $contents;
    }
}
