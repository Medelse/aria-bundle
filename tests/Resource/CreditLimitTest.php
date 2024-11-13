<?php

namespace Medelse\AriaBundle\Tests\Resource;

use Medelse\AriaBundle\Resource\CreditLimit;
use Medelse\AriaBundle\Security\BearerGenerator;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class CreditLimitTest extends TestCase
{
    public const API_URL = 'https://foo.bar';

    public function testGetCreditLimitRequest()
    {
        $mockedResponse = new MockResponse(json_encode([
            'amount' => 10,
            'outstandingAmount' => 30,
        ]));
        $expectedURL = self::API_URL . CreditLimit::GET_FROM_SIREN;
        $httpClient = new MockHttpClient($mockedResponse, 'https://example.com');

        $resource = $this->setupCreditLimitResource($httpClient);

        $response = $resource->getFromSiren('1234');

        $this->assertSame(1, $httpClient->getRequestsCount());
        $this->assertSame('GET', $mockedResponse->getRequestMethod());
        $this->assertContains(
            'authorization: Bearer ',
            $mockedResponse->getRequestOptions()['headers'],
        );
        $this->assertCount(1, $mockedResponse->getRequestOptions()['query']);
        $this->assertStringContainsString(
            'like(debtorIdentifier.value,1234%)',
            $mockedResponse->getRequestOptions()['query']['filter'],
        );
        $this->assertStringStartsWith($expectedURL,$mockedResponse->getRequestUrl());

        $this->assertSame(2, count($response));
        $this->assertSame($response['amount'], 10);
        $this->assertSame($response['outstandingAmount'], 30);
    }

    public function testGetCreditLimitReturnsError()
    {
        $response = new MockResponse(json_encode([
            'status' => '400',
            'message' => 'Something\'s wrong',
            'code' => 'BAD_REQUEST',
        ]));
        $httpClient = new MockHttpClient($response, 'https://example.com');
        $resource = $this->setupCreditLimitResource($httpClient);
        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage('Error 400 BAD_REQUEST: Something\'s wrong');

        $resource->getFromSiren('fizz');
    }

    private function setupCreditLimitResource(HttpClientInterface $httpClient): CreditLimit
    {
        $bearerGenerator = $this->createMock(BearerGenerator::class);
        return new CreditLimit($httpClient, $bearerGenerator, self::API_URL);
    }
}
