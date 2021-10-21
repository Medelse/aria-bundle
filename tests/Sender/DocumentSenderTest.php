<?php

namespace Medelse\AriaBundle\Tests\Sender;

use Medelse\AriaBundle\Sender\DocumentSender;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class DocumentSenderTest extends TestCase
{
    public function testSendPutRequestFormData()
    {
        $response = new MockResponse(json_encode(['response' => 'Good job']));
        $httpClient = new MockHttpClient($response, 'https://example.com');

        $sender = new DocumentSender($httpClient, '', 'ariaApiKey');
        $response = $sender->sendDocumentId($this->getDocument(), 'ariaId');

        $this->assertIsArray($response);
        $this->assertArrayHasKey('response', $response);
        $this->assertEquals('Good job', $response['response']);
    }

    public function testSendPutRequestFormDataReturnsError()
    {
        $response = new MockResponse(json_encode([
            'status' => '400',
            'message' => 'Something\'s wrong',
            'code' => 'BAD_REQUEST',
        ]));
        $httpClient = new MockHttpClient($response, 'https://example.com');

        $sender = new DocumentSender($httpClient, '', 'ariaApiKey');
        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage('Error 400 BAD_REQUEST: Something\'s wrong');
        $response = $sender->sendDocumentId($this->getDocument(), 'ariaId');
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
