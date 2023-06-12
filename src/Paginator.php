<?php

namespace OpenPix\PhpSdk;

use OpenPix\PhpSdk\Request;
use TypeError;

/**
 * Pagination wrapper for listing requests.
 *
 * @phpstan-type Pagination array{skip: int, limit: int, totalCount: int, hasPreviousPage: bool, hasNextPage: bool}
 */
class Paginator
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

    public function perPage(int $perPage): self
    {
        $this->perPage = $perPage;
        return $this;
    }

    public function skip(int $skip): self
    {
        $this->skip = $skip;
        return $this;
    }

    /**
     * @return array<mixed>
     */
    public function next(): array
    {
        $this->skip += $this->perPage;
        return $this->sendRequest();
    }

    /**
     * @return array<mixed>
     */
    public function previous(): array
    {
        $this->skip -= $this->perPage;
        return $this->sendRequest();
    }

    /**
     * @return array<mixed>
     */
    public function go(int $page): array
    {
        $this->skip = $page * $this->perPage;
        return $this->sendRequest();
    }

    /**
     * Send request to the list endpoint using current parameters.
     *
     * @return array<mixed>
     */
    public function sendRequest(): array
    {
        return $this->lastResult = $this->requestTransport->transport($this->getPagedRequest());
    }

    public function getPagedRequest(): Request
    {
        return $this->listRequest->pagination($this->skip, $this->perPage);
    }

    /**
     * @return Pagination
     */
    private function getPagination(): array
    {
        $lastResult = $this->lastResult ?? $this->sendRequest();

        if (empty($lastResult["pageInfo"])) {
            throw new TypeError("The request to the endpoint does not support paging.");
        }

        /** @var array{pageInfo: Pagination} $lastResult */
        return $lastResult["pageInfo"];
    }

    public function getTotalCount(): int
    {
        return (int) $this->getPagination()["totalCount"];
    }

    public function hasPreviousPage(): bool
    {
        return (bool) $this->getPagination()["hasPreviousPage"];
    }

    public function hasNextPage(): bool
    {
        return (bool) $this->getPagination()["hasNextPage"];
    }
}
