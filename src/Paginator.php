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
     * Get current result.
     *
     * @return array<mixed>
     */
    public function current(): array
    {
        if (is_null($this->lastResult)) return $this->update();

        return $this->lastResult;
    }

    /**
     * Go to first page and update the current result.
     */
    public function rewind(): void
    {
        $this->skip = 0;
        $this->update();
    }

    /**
     * Go to next page and update the current result.
     */
    public function next(): void
    {
        $this->skip += $this->perPage;
        $this->update();
    }

    /**
     * Go to previous page and update the current result.
     */
    public function previous(): void
    {
        $this->skip -= $this->perPage;
        $this->update();
    }

    /**
     * Changes the current page and update the current result.
     */
    public function go(int $page): void
    {
        $this->skip = $page * $this->perPage;
        $this->update();
    }

    /**
     * Returns true if it is possible to change to another page besides the current one.
     */
    public function valid(): bool
    {
        $pagination = $this->getPagination();

        return $pagination["hasPreviousPage"] || $pagination["hasNextPage"];
    }

    /**
     * Update current pagination result.
     *
     * @return array<mixed>
     */
    public function update(): array
    {
        return $this->lastResult = $this->requestTransport->transport($this->getPagedRequest());
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
        $lastResult = $this->lastResult ?? $this->update();

        if (empty($lastResult["pageInfo"])) {
            throw new TypeError("Endpoint does not support pagination.");
        }

        /** @var array{pageInfo: Pagination} $lastResult */
        return $lastResult["pageInfo"];
    }
}
