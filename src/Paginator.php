<?php

namespace OpenPix\PhpSdk;

use Iterator;
use OpenPix\PhpSdk\Request;
use TypeError;

/**
 * Pagination wrapper for listing requests.
 *
 * ## Usage
 * ```php
 * $paginator = $client->charges()->list();
 *
 * // Current page is 0
 * $result = $paginator->current();
 *
 * $result["charges"][0]["type"]; // type of first charge
 *
 * // Navigate with paginator
 * $paginator->next(); // move to next page
 * $paginator->previous(); // move to previous page
 * $paginator->go(1); // go to page 1
 *
 * // Get result from current page
 * $result = $paginator->current();
 * ```
 *
 * @implements Iterator<int, array<mixed>>
 * @phpstan-type Pagination array{skip: int, limit: int, totalCount: int, hasPreviousPage: bool, hasNextPage: bool}
 */
class Paginator implements Iterator
{
    /**
     * Transport used by HTTP requests.
     *
     * @var RequestTransport
     */
    private $requestTransport;

    /**
     * Last request sent to API.
     *
     * @var Request
     */
    private $listRequest;

    /**
     * Last result from API.
     *
     * @var array<mixed>|null
     */
    private $lastResult;

    /**
     * Amount of resources to be skipped.
     *
     * @var int
     */
    private $skip = 0;

    /**
     * Amount of resources per page.
     *
     * @var int
     */
    private $perPage = 30;

    /**
     * Create a new `Paginator` instance.
     *
     * @param RequestTransport $requestTransport Transport used by HTTP requests.
     * @param Request $listRequest Request used to perform paging, through data such
     * as URI, HTTP method and query parameters.
     * @param array<string, mixed>|null $lastResult Result of the last call to the API or
     * null if no request has been sent so far.
     */
    public function __construct(
        RequestTransport $requestTransport,
        Request $listRequest,
        array $lastResult = null
    ) {
        $this->requestTransport = $requestTransport;
        $this->listRequest = $listRequest;
        $this->lastResult = $lastResult;
    }

    /**
     * Changes the maximum amount of resources per page.
     */
    public function perPage(int $perPage): self
    {
        $this->perPage = $perPage;
        return $this;
    }

    /**
     * Get maximum amount of resources per page.
     */
    public function getPerPageCount(): int
    {
        return $this->perPage;
    }

    /**
     * Skips an amount of resources.
     */
    public function skip(int $skip): self
    {
        $this->skip = $skip;
        return $this;
    }

    /**
     * Get amount of resources skipped.
     */
    public function getSkippedCount(): int
    {
        return $this->skip;
    }

    /**
     * Get current page number.
     */
    public function key(): int
    {
        return intval($this->skip / $this->perPage);
    }

    /**
     * Retrieve current result by sending a HTTP request with well-formed
     * pagination parameters to API.
     *
     * @return array<mixed>
     */
    public function current(): array
    {
        return $this->lastResult = $this->requestTransport->transport($this->getPagedRequest());
    }

    /**
     * Go to first page.
     */
    public function rewind(): void
    {
        $this->skip(0);
    }

    /**
     * Go to next page.
     */
    public function next(): void
    {
        $this->skip($this->skip + $this->perPage);
    }

    /**
     * Go to previous page.
     */
    public function previous(): void
    {
        $this->skip($this->skip - $this->perPage);
    }

    /**
     * Changes the current page.
     */
    public function go(int $page): void
    {
        $this->skip($page * $this->perPage);
    }

    /**
     * Returns true if has next page.
     */
    public function valid(): bool
    {
        if (is_null($this->lastResult)) {
            return true;
        }

        return $this->getPagination()["hasNextPage"];
    }

    /**
     * Applies the pagination parameters to the listing request and returns it.
     */
    public function getPagedRequest(): Request
    {
        return $this->listRequest->pagination($this->skip, $this->perPage);
    }

    /**
     * Gets the total amount of resources (eg customers, webhooks, charges, etc.)
     * on this endpoint.
     */
    public function getTotalResourcesCount(): int
    {
        return $this->getPagination()["totalCount"];
    }

    /**
     * Get pagination metadata.
     *
     * @return Pagination
     */
    private function getPagination(): array
    {
        $lastResult = $this->lastResult ?? $this->current();

        if (empty($lastResult["pageInfo"])) {
            throw new TypeError("Endpoint does not support pagination.");
        }

        /** @var array{pageInfo: Pagination} $lastResult */
        return $lastResult["pageInfo"];
    }
}
