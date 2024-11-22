<?php

namespace Tests\Resources;

use OpenPix\PhpSdk\Request;
use OpenPix\PhpSdk\RequestTransport;
use OpenPix\PhpSdk\Resources\Webhooks;
use PHPUnit\Framework\TestCase;

final class WebhooksTest extends TestCase
{
    public function testList(): void
    {
        $requestTransportMock = $this->createMock(RequestTransport::class);

        $webhooks = new Webhooks($requestTransportMock);
        $pagedRequest = $webhooks->list("https://example.com")
            ->perPage(15)
            ->skip(42)
            ->getPagedRequest();

        $this->assertSame($pagedRequest->getPath(), "/api/v1/webhook");
        $this->assertSame($pagedRequest->getMethod(), "GET");
        $this->assertSame($pagedRequest->getBody(), null);
        $this->assertSame($pagedRequest->getQueryParams(), [
            "url" => "https://example.com",
            "skip" => 42,
            "limit" => 15,
        ]);
    }

    public function testDelete(): void
    {
        $requestTransportMock = $this->createMock(RequestTransport::class);
        $requestTransportMock->expects($this->once())
            ->method("transport")
            ->willReturnCallback(function (Request $request) {
                $this->assertSame("DELETE", $request->getMethod());
                $this->assertSame("/api/v1/webhook/abcd", $request->getPath());
                $this->assertSame($request->getBody(), null);
                $this->assertSame($request->getQueryParams(), []);

                return ["status" => ""];
            });

        $webhooks = new Webhooks($requestTransportMock);
        $result = $webhooks->delete("abcd");

        $this->assertSame($result, ["status" => ""]);
    }

    public function testCreate(): void
    {
        $requestBody = [
            "webhook" => [],
        ];

        $requestTransportMock = $this->createMock(RequestTransport::class);
        $requestTransportMock->expects($this->once())
            ->method("transport")
            ->willReturnCallback(function (Request $request) use ($requestBody) {
                $this->assertSame("POST", $request->getMethod());
                $this->assertSame("/api/v1/webhook", $request->getPath());
                $this->assertSame($request->getBody(), $requestBody);
                $this->assertSame($request->getQueryParams(), []);

                return ["webhook" => []];
            });

        $webhooks = new Webhooks($requestTransportMock);
        $result = $webhooks->create($requestBody);

        $this->assertSame(["webhook" => []], $result);
    }

    public function testIps(): void
    {
        $requestTransportMock = $this->createMock(RequestTransport::class);
        $requestTransportMock->expects($this->once())
            ->method("transport")
            ->willReturnCallback(function (Request $request) {
                $this->assertSame("GET", $request->getMethod());
                $this->assertSame("/api/v1/webhook/ips", $request->getPath());
                $this->assertSame(null, $request->getBody());
                $this->assertSame([], $request->getQueryParams());

                return ["ips" => []];
            });

        $webhooks = new Webhooks($requestTransportMock);
        $result = $webhooks->ips();

        $this->assertSame(["ips" => []], $result);
    }

    public function testIsWebhookValidReturnsTrueWithValidWebhook(): void
    {
        $webhooks = new Webhooks($this->createStub(RequestTransport::class));

        $this->assertTrue(
            $webhooks->isWebhookValid(
                $this->getWebhookPayloadFixture(),
                $this->getWebhookSignatureFixture()
            )
        );
    }

    public function testIsWebhookValidReturnsFalseWithInvalidSignature(): void
    {
        $webhooks = new Webhooks($this->createStub(RequestTransport::class));

        $this->assertFalse($webhooks->isWebhookValid($this->getWebhookPayloadFixture(), "invalid-signature"));
    }

    public function testIsWebhookValidReturnsFalseWithInvalidPayload(): void
    {
        $webhooks = new Webhooks($this->createStub(RequestTransport::class));

        $this->assertFalse($webhooks->isWebhookValid("invalid-payload", $this->getWebhookSignatureFixture()));
    }

    /**
     * @link https://developers.openpix.com.br/docs/webhook/webhook-signature-validation
     */
    private function getWebhookPayloadFixture(): string
    {
        return <<<JSON
        { "pixQrCode": null, "charge": { "status": "COMPLETED", "customer": { "name": "Antonio Victor", "taxID": { "taxID": "12345678976", "type": "BR:CPF" }, "email": "antoniocliente@example.com", "correlationID": "4979ceba-2132-4292-bd90-bee7fb2125e4" }, "value": 1000, "comment": "Pagamento OpenPix", "transactionID": "ea83401ed4834b3ea6f1f283b389af29", "correlationID": "417bae21-3d08-4cdb-9c2d-fee63c89e9e4", "paymentLinkID": "34697ed2-3790-4b60-8512-e7465b142d84", "createdAt": "2021-03-12T12:43:54.528Z", "updatedAt": "2021-03-12T12:44:09.360Z", "brCode": "https://api.openpix.com.br/openpix/openpix/testing?transactionID=ea83401ed4834b3ea6f1f283b389af29" }, "pix": { "charge": { "status": "COMPLETED", "customer": { "name": "Antonio Victor", "taxID": { "taxID": "12345678976", "type": "BR:CPF" }, "email": "antoniocliente@example.com", "correlationID": "4979ceba-2132-4292-bd90-bee7fb2125e4" }, "value": 1000, "comment": "Pagamento OpenPix", "transactionID": "ea83401ed4834b3ea6f1f283b389af29", "correlationID": "417bae21-3d08-4cdb-9c2d-fee63c89e9e4", "paymentLinkID": "34697ed2-3790-4b60-8512-e7465b142d84", "createdAt": "2021-03-12T12:43:54.528Z", "updatedAt": "2021-03-12T12:44:09.360Z" }, "customer": { "correlationID": "9134e286-6f71-427a-bf00-241681624586", "email": "email1@example.com", "name": "Loma", "phone": "+5511999999999", "taxID": { "taxID": "47043622050", "type": "BR:CPF" } }, "payer": { "correlationID": "9134e286-6f71-427a-bf00-241681624586", "email": "email1@example.com", "name": "Loma", "phone": "+5511999999999", "taxID": { "taxID": "47043622050", "type": "BR:CPF" } }, "time": "2021-03-12T12:44:09.269Z", "value": 1, "transactionID": "ea83401ed4834b3ea6f1f283b389af29", "infoPagador": "OpenPix testing" }, "company": { "id": "624f46f9e93f9f521c8308d7", "name": "Pizzaria do José", "taxID": "4722767300014" }, "account": { "clientId": "ZOJ64B9B-ZM1W-89MI-4UCI-OP2LVIU6NY75" } }
        JSON;
    }

    /**
     * @link https://developers.openpix.com.br/docs/webhook/webhook-signature-validation
     */
    private function getWebhookSignatureFixture(): string
    {
        return "lL2nnXgmLFGgxJ8+jCDguqouU4ucrIxYJcU5SPrJFaNcJajTJHYVldqc/z4YFIjAjtPEALe699WosgPY08W7CLpidvtm06Qwa4YMB0l/DcTS93O91NdSH/adjugEKiOb76Zj/0jB8mqOmWCFYbweOBa17bssuEkd5Lw7Q5L314Y=";
    }
}
