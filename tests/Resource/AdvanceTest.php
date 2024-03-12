<?php

namespace Medelse\AriaBundle\Tests\Resource;

use Medelse\AriaBundle\Resolver\Advance\CreateAdvanceResolver;
use Medelse\AriaBundle\Resource\Advance;
use Medelse\AriaBundle\Resource\Document;
use Medelse\AriaBundle\Security\BearerGenerator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class AdvanceTest extends TestCase
{
    public function testCreateAdvance()
    {
        $response = new MockResponse(json_encode(['response' => 'Good job']));
        $httpClient = new MockHttpClient($response, 'https://example.com');
        $bearerGenerator = $this->createMock(BearerGenerator::class);

        $advanceResource = new Advance($httpClient, $bearerGenerator, 'https://api.sandbox.helloaria.eu');
        $response = $advanceResource->createAdvance($this->getAdvance(), 'ariaId');

        $this->assertIsArray($response);
        $this->assertArrayHasKey('response', $response);
        $this->assertEquals('Good job', $response['response']);
    }

    public function testCreateAdvanceReturnsError()
    {
        $response = new MockResponse(json_encode([
            'status' => '400',
            'message' => 'Something\'s wrong',
            'code' => 'BAD_REQUEST',
        ]));
        $httpClient = new MockHttpClient($response, 'https://example.com');
        $bearerGenerator = $this->createMock(BearerGenerator::class);

        $advanceResource = new Advance($httpClient, $bearerGenerator, 'https://api.sandbox.helloaria.eu');
        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage('Error 400 BAD_REQUEST: Something\'s wrong');
        $advanceResource->createAdvance($this->getAdvance(), 'ariaId');
    }

    private function getAdvance(): array
    {
        return [
            'billDate' => (new \DateTime())->setTime(0, 0, 0),
            'amount' => 500,
            'currency' => CreateAdvanceResolver::CURRENCY_EUR,
            'duration' => 60 * 60 * 60 * 24 * 1000,
            'customerName' => 'Hannibal Lecter',
            'customerSiren' => '999555999',
            'invoiceNumber' => '191254',
            'label' => 'The silence of the lambs',
            'bill' => [
                [
                    'document' => 'Clarice Starling invoice',
                    'fileName' => 'clarice_starling_invoice',
                ],
                [
                    'document' => 'Jack Crawford invoice',
                    'fileName' => 'jack_crawford_invoice',
                ],
            ],
        ];
    }
}
