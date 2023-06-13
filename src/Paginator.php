<?php

namespace OpenPix\PhpSdk;

use Iterator;
use OpenPix\PhpSdk\Request;
use TypeError;

/**
 * Pagination wrapper for listing requests.
 *
 * @implements Iterator<int, array<mixed>>
 * @phpstan-type Pagination array{skip: int, limit: int, totalCount: int, hasPreviousPage: bool, hasNextPage: bool}
 */
class Paginator implements Iterator
{
    private RequestTransport $requestTransport;

    private Request $listRequest;

    /**
     * @var array<mixed>|null
     */
    private ?array $lastResult;

    private int $skip = 0;

    private int $perPage = 30;

    /**
     * @param array<mixed>|null $lastResult
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
     * Skips an amount of resources.
     */
    public function skip(int $skip): self
    {
        $this->skip = $skip;
        return $this;
    }

    /**
     * Get current page number.
     */
    public function key(): int
    {
        return intval($this->skip / $this->perPage);
    }

    /**
     * Retrieve current result.
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
        if (is_null($this->lastResult)) return true;

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
