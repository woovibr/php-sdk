<?php

namespace OpenPix\PhpSdk;

use OpenPix\PhpSdk\Request;

/**
 * Pagination wrapper for listing requests.
 *
 * @template Result
 */
class Paginator
{
    private RequestTransport $requestTransport;

    private Request $listRequest;

    /**
     * @var Result
     */
    private ?array $lastResult;

    private int $skip = 0;

    private int $perPage = 30;

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
     * @return Result
     */
    public function next(): array
    {
        $this->skip += $this->perPage;
        return $this->sendRequest();
    }

    /**
     * @return Result
     */
    public function previous(): array
    {
        $this->skip -= $this->perPage;
        return $this->sendRequest();
    }

    /**
     * @return Result
     */
    public function go(int $page): array
    {
        $this->skip = $page * $this->perPage;
        return $this->sendRequest();
    }

    /**
     * Send request to the list endpoint using current parameters.
     *
     * @return Result
     */
    public function sendRequest(): array
    {
        return $this->lastResult = $this->requestTransport->transport($this->getPagedRequest());
    }

    public function getPagedRequest(): Request
    {
        return $this->listRequest->pagination($this->skip, $this->perPage);
    }

    private function getPagination(): array
    {
        if (is_null($this->lastResult)) {
            return $this->sendRequest()["pageInfo"];
        }

        return $this->lastResult["pageInfo"];
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
