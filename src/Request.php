<?php

namespace OpenPix\PhpSdk;

use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;

/**
 * Fluent builder for API requests.
 */
class Request
{
    /**
     * The path to endpoint.
     *
     * @var string
     */
    private $path;

    /**
     * The HTTP method of endpoint.
     *
     * @var string
     */
    private $method;

    /**
     * Query parameters to set into URI on request build.
     *
     * @var array<mixed>
     */
    private $queryParams = [];

    /**
     * Body of request.
     *
     * Can be an array for JSON requests, \Psr\Http\Message\StreamInterface
     * for customization on request body or null for requests without body.
     *
     * @var array<mixed>|\Psr\Http\Message\StreamInterface|null
     */
    private $body = null;

    /**
     * Set HTTP method.
     */
    public function method(string $method): self
    {
        $this->method = $method;
        return $this;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * Set request path.
     */
    public function path(string $path): self
    {
        $this->path = $path;
        return $this;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Update all query parameters with given array.
     *
     * @param array<string, mixed> $queryParams
     */
    public function queryParams(array $queryParams): self
    {
        $this->queryParams = $queryParams;
        return $this;
    }

    /**
     * Return query parameters.
     *
     * @return array<string, mixed>
     */
    public function getQueryParams(): array
    {
        return $this->queryParams;
    }

    /**
     * Set request body.
     *
     * @param array<mixed>|\Psr\Http\Message\StreamInterface $body
     */
    public function body($body): self
    {
        $this->body = $body;
        return $this;
    }

    /**
     * Get request body.
     *
     * @return array<mixed>|\Psr\Http\Message\StreamInterface|null
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Apply query parameters for pagination.
     */
    public function pagination(int $skip, int $limit): self
    {
        $this->queryParams["skip"] = $skip;
        $this->queryParams["limit"] = $limit;
        return $this;
    }

    /**
     * Return an PSR-7 HTTP request for PSR-18 HTTP clients.
     */
    public function build(
        string $baseUri,
        RequestFactoryInterface $requestFactory,
        StreamFactoryInterface $streamFactory
    ): RequestInterface {
        $uri = $baseUri . $this->path;

        if (!empty($this->queryParams)) {
            $uri .= "?" . http_build_query($this->normalizeQueryParams($this->queryParams));
        }

        $request = $requestFactory->createRequest($this->method, $uri);

        $request = $this->injectBody($request, $streamFactory);

        return $request;
    }

    /**
     * Inject body into request as PSR-7 Stream, with proper `Content-type` header.
     */
    private function injectBody(RequestInterface $request, StreamFactoryInterface $streamFactory): RequestInterface
    {
        if (is_null($this->body)) {
            return $request;
        }

        if (!($this->body instanceof StreamInterface)) {
            $stream = $streamFactory->createStream(json_encode($this->body, JSON_THROW_ON_ERROR));

            return $request->withAddedHeader("Content-type", "application/json")
                ->withBody($stream);
        }

        return $request->withBody($this->body);
    }

    /**
     * Normalize query parameters. For example, it converts boolean parameters to strings,
     * as `http_build_query` returns integers.
     *
     * @param array<mixed> $queryParams
     * @return array<mixed>
     */
    private function normalizeQueryParams(array $queryParams): array
    {
        foreach ($queryParams as $name => $value) {
            if (is_bool($value)) {
                $value = $value ? "true" : "false";
            }

            $queryParams[$name] = $value;
        }

        return $queryParams;
    }
}
