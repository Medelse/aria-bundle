<?php

namespace Medelse\AriaBundle\Tests\Resource;

use Medelse\AriaBundle\Resource\Document;
use Medelse\AriaBundle\Security\BearerGenerator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class DocumentTest extends TestCase
{
    public function testSendDocumentId()
    {
        $response = new MockResponse(json_encode(['response' => 'Good job']));
        $httpClient = new MockHttpClient($response, 'https://example.com');
        $bearerGenerator = $this->createMock(BearerGenerator::class);

        $documentResource = new Document($httpClient, $bearerGenerator, 'clientId', 'clientSecret', 'https://api.sandbox.helloaria.eu');
        $response = $documentResource->sendDocumentId($this->getDocument(), 'ariaId');

        $this->assertIsArray($response);
        $this->assertArrayHasKey('response', $response);
        $this->assertEquals('Good job', $response['response']);
    }

    public function testSendDocumentsId()
    {
        $response = new MockResponse(json_encode(['response' => 'Good job']));
        $httpClient = new MockHttpClient($response, 'https://example.com');
        $bearerGenerator = $this->createMock(BearerGenerator::class);

        $documentResource = new Document($httpClient, $bearerGenerator, 'clientId', 'clientSecret', 'https://api.sandbox.helloaria.eu');
        $response = $documentResource->sendDocumentsId([$this->getDocument(),$this->getDocument()], 'ariaId');

        $this->assertIsArray($response);
        $this->assertArrayHasKey('response', $response);
        $this->assertEquals('Good job', $response['response']);
    }

    public function testSendDocumentIdReturnsError()
    {
        $response = new MockResponse(json_encode([
            'status' => '400',
            'message' => 'Something\'s wrong',
            'code' => 'BAD_REQUEST',
        ]));
        $httpClient = new MockHttpClient($response, 'https://example.com');
        $bearerGenerator = $this->createMock(BearerGenerator::class);

        $documentResource = new Document($httpClient, $bearerGenerator, 'clientId', 'clientSecret', 'https://api.sandbox.helloaria.eu');
        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage('Error 400 BAD_REQUEST: Something\'s wrong');
        $documentResource->sendDocumentId($this->getDocument(), 'ariaId');
    }

    /**
     *
     * PRIVATE
     *
     */

    private function getDocument(): array
    {
        return [
            'document' => 'string',
            'fileName' => 'string',
            'mimeType' => 'image/jpeg',
        ];
    }
}
