<?php

namespace Medelse\AriaBundle\Tests\Resource;

use Medelse\AriaBundle\Resource\Upload;
use Medelse\AriaBundle\Security\BearerGenerator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

class UploadTest extends TestCase
{
    public function testUploadFileSuccess()
    {
        $response = new MockResponse(json_encode(['response' => [['url' => 'https://example.com/file']]]));
        $httpClient = new MockHttpClient($response, 'https://example.com');
        $bearerGenerator = $this->createMock(BearerGenerator::class);

        $uploadResource = new Upload($httpClient, $bearerGenerator, 'https://api.sandbox.helloaria.eu');
        $response = $uploadResource->uploadFile($this->getFile());

        $this->assertIsArray($response);
        $this->assertArrayHasKey('response', $response);
        $this->assertEquals('https://example.com/file', $response['response'][0]['url']);
    }

    private function getFile(): array
    {
        return [
            'document' => 'Clarice Starling file.pdf',
            'contentType' => 'application/pdf',
            'fileName' => 'Clarice Starling file',
        ];
    }
}
