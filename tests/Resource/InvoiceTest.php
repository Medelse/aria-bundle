<?php

namespace Medelse\AriaBundle\Tests\Resource;

use Medelse\AriaBundle\Resource\Invoice as InvoiceResource;
use Medelse\AriaBundle\Security\BearerGenerator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class InvoiceTest extends TestCase
{
    public function testCreateInvoice()
    {
        $response = new MockResponse(json_encode([
            'id' => 12,
        ]));
        $httpClient = new MockHttpClient($response, 'https://example.com');
        $bearerGenerator = $this->createMock(BearerGenerator::class);

        $invoiceResource = new InvoiceResource($httpClient, $bearerGenerator, 'https://api.sandbox.helloaria.eu');
        $response = $invoiceResource->createInvoice($this->getInvoice());

        $this->assertIsArray($response);
        $this->assertArrayHasKey('id', $response);
        $this->assertEquals(12, $response['id']);
    }

    public function testCreateInvoiceReturnsError()
    {
        $response = new MockResponse(json_encode([
            'status' => '400',
            'message' => 'Something\'s wrong',
            'code' => 'BAD_REQUEST',
        ]));
        $httpClient = new MockHttpClient($response, 'https://example.com');
        $bearerGenerator = $this->createMock(BearerGenerator::class);

        $invoiceResource = new InvoiceResource($httpClient, $bearerGenerator, 'https://api.sandbox.helloaria.eu');
        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage('Error 400 BAD_REQUEST: Something\'s wrong');
        $invoiceResource->createInvoice($this->getInvoice());
    }


    public function testUpdateInvoice()
    {
        $response = new MockResponse(json_encode([
            'id' => 12,
        ]));
        $httpClient = new MockHttpClient($response, 'https://example.com');
        $bearerGenerator = $this->createMock(BearerGenerator::class);

        $invoiceResource = new InvoiceResource($httpClient, $bearerGenerator, 'https://api.sandbox.helloaria.eu');
        $response = $invoiceResource->updateInvoice(1, $this->getInvoiceDataForUpdate());

        $this->assertIsArray($response);
        $this->assertArrayHasKey('id', $response);
        $this->assertEquals(12, $response['id']);
    }

    public function testUpdateInvoiceReturnsError()
    {
        $response = new MockResponse(json_encode([
            'status' => '400',
            'message' => 'Something\'s wrong',
            'code' => 'BAD_REQUEST',
        ]));
        $httpClient = new MockHttpClient($response, 'https://example.com');
        $bearerGenerator = $this->createMock(BearerGenerator::class);

        $invoiceResource = new InvoiceResource($httpClient, $bearerGenerator, 'https://api.sandbox.helloaria.eu');
        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage('Error 400 BAD_REQUEST: Something\'s wrong');
        $invoiceResource->updateInvoice(1, $this->getInvoiceDataForUpdate());
    }

    public function testGetInvoiceRequest()
    {
        $response = new MockResponse(json_encode(['response' => 'Good job']));
        $httpClient = new MockHttpClient($response, 'https://example.com');
        $bearerGenerator = $this->createMock(BearerGenerator::class);

        $invoiceResource = new InvoiceResource($httpClient, $bearerGenerator, 'https://api.sandbox.helloaria.eu');
        $response = $invoiceResource->getInvoice(1);

        $this->assertIsArray($response);
        $this->assertArrayHasKey('response', $response);
        $this->assertEquals('Good job', $response['response']);
    }

    public function testGetInvoiceReturnsError()
    {
        $response = new MockResponse(json_encode([
            'status' => '400',
            'message' => 'Something\'s wrong',
            'code' => 'BAD_REQUEST',
        ]));
        $httpClient = new MockHttpClient($response, 'https://example.com');
        $bearerGenerator = $this->createMock(BearerGenerator::class);

        $invoiceResource = new InvoiceResource($httpClient, $bearerGenerator, 'https://api.sandbox.helloaria.eu');
        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage('Error 400 BAD_REQUEST: Something\'s wrong');
        $invoiceResource->getInvoice(1);
    }

    public function testDeleteInvoiceRequest()
    {
        $response = new MockResponse(json_encode(['response' => 'Good job']));
        $httpClient = new MockHttpClient($response, 'https://example.com');
        $bearerGenerator = $this->createMock(BearerGenerator::class);

        $invoiceResource = new InvoiceResource($httpClient, $bearerGenerator, 'https://api.sandbox.helloaria.eu');
        $response = $invoiceResource->deleteInvoice(1);

        $this->assertIsArray($response);
        $this->assertArrayHasKey('response', $response);
        $this->assertEquals('Good job', $response['response']);
    }

    public function testDeleteInvoiceReturnsError()
    {
        $response = new MockResponse(json_encode([
            'status' => '400',
            'message' => 'Something\'s wrong',
            'code' => 'BAD_REQUEST',
        ]));
        $httpClient = new MockHttpClient($response, 'https://example.com');
        $bearerGenerator = $this->createMock(BearerGenerator::class);

        $invoiceResource = new InvoiceResource($httpClient, $bearerGenerator, 'https://api.sandbox.helloaria.eu');
        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage('Error 400 BAD_REQUEST: Something\'s wrong');
        $invoiceResource->deleteInvoice(1);
    }

    private function getInvoice(): array
    {
        return [
            'debtorId' => null,
            'attachments' => [['url' => 'https://sample.com']],
            'totalAmountIncludingTaxes' => 100,
            'totalAmountExcludingTaxes' => 90,
            'currency' => 'EUR',
            'invoiceDate' => new \DateTime(),
            'invoiceNumber' => '12340',
            'invoiceOwner' => null,
            'debtorName' => 'Leeroy Jenkins',
            'debtorIdentifier' => ['type' => 'siren', 'value' => '999555999'],
            'contacts' => [['email' => 'test@sample.com', 'phoneNumber' => '+33606060606']],
            'debtorMetadata' => null,
            'repaymentPeriod' => 30,
        ];
    }

    private function getInvoiceDataForUpdate(): array
    {
        return [
            'totalAmountIncludingTaxes' => 110,
            'totalAmountExcludingTaxes' => 100,
        ];
    }
}
