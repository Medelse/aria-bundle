<?php

namespace Medelse\AriaBundle\Tests\Resource;

use Medelse\AriaBundle\Resource\Loan as LoanResource;
use Medelse\AriaBundle\Security\BearerGenerator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class LoanTest extends TestCase
{
    public function testCreateLoan()
    {
        $response = new MockResponse(json_encode([
            'id' => 12,
            'status' => 'CREATED',
        ]));
        $httpClient = new MockHttpClient($response, 'https://example.com');
        $bearerGenerator = $this->createMock(BearerGenerator::class);

        $loanResource = new LoanResource($httpClient, $bearerGenerator, 'https://api.sandbox.helloaria.eu');
        $response = $loanResource->createLoan($this->getLoan());

        $this->assertIsArray($response);
        $this->assertArrayHasKey('id', $response);
        $this->assertArrayHasKey('status', $response);
        $this->assertEquals(12, $response['id']);
        $this->assertEquals('CREATED', $response['status']);
    }

    public function testCreateLoanReturnsError()
    {
        $response = new MockResponse(json_encode([
            'status' => '400',
            'message' => 'Something\'s wrong',
            'code' => 'BAD_REQUEST',
        ]));
        $httpClient = new MockHttpClient($response, 'https://example.com');
        $bearerGenerator = $this->createMock(BearerGenerator::class);

        $loanResource = new LoanResource($httpClient, $bearerGenerator, 'https://api.sandbox.helloaria.eu');
        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage('Error 400 BAD_REQUEST: Something\'s wrong');
        $loanResource->createLoan($this->getLoan());
    }


    public function testUpdateLoan()
    {
        $response = new MockResponse(json_encode([
            'id' => 12,
        ]));
        $httpClient = new MockHttpClient($response, 'https://example.com');
        $bearerGenerator = $this->createMock(BearerGenerator::class);

        $loanResource = new LoanResource($httpClient, $bearerGenerator, 'https://api.sandbox.helloaria.eu');
        $response = $loanResource->updateLoan(1, $this->getLoanDataForUpdate());

        $this->assertIsArray($response);
        $this->assertArrayHasKey('id', $response);
        $this->assertEquals(12, $response['id']);
    }

    public function testUpdateLoanReturnsError()
    {
        $response = new MockResponse(json_encode([
            'status' => '400',
            'message' => 'Something\'s wrong',
            'code' => 'BAD_REQUEST',
        ]));
        $httpClient = new MockHttpClient($response, 'https://example.com');
        $bearerGenerator = $this->createMock(BearerGenerator::class);

        $loanResource = new LoanResource($httpClient, $bearerGenerator, 'https://api.sandbox.helloaria.eu');
        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage('Error 400 BAD_REQUEST: Something\'s wrong');
        $loanResource->updateLoan(1, $this->getLoanDataForUpdate());
    }

    public function testGetLoanRequest()
    {
        $response = new MockResponse(json_encode(['response' => 'Good job']));
        $httpClient = new MockHttpClient($response, 'https://example.com');
        $bearerGenerator = $this->createMock(BearerGenerator::class);

        $loanResource = new LoanResource($httpClient, $bearerGenerator, 'https://api.sandbox.helloaria.eu');
        $response = $loanResource->getLoan(1);

        $this->assertIsArray($response);
        $this->assertArrayHasKey('response', $response);
        $this->assertEquals('Good job', $response['response']);
    }

    public function testGetLoanReturnsError()
    {
        $response = new MockResponse(json_encode([
            'status' => '400',
            'message' => 'Something\'s wrong',
            'code' => 'BAD_REQUEST',
        ]));
        $httpClient = new MockHttpClient($response, 'https://example.com');
        $bearerGenerator = $this->createMock(BearerGenerator::class);

        $loanResource = new LoanResource($httpClient, $bearerGenerator, 'https://api.sandbox.helloaria.eu');
        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage('Error 400 BAD_REQUEST: Something\'s wrong');
        $loanResource->getLoan(1);
    }

    public function testRetryPaymentLoanRequest()
    {
        $response = new MockResponse(json_encode(['response' => 'Good job']));
        $httpClient = new MockHttpClient($response, 'https://example.com');
        $bearerGenerator = $this->createMock(BearerGenerator::class);

        $loanResource = new LoanResource($httpClient, $bearerGenerator, 'https://api.sandbox.helloaria.eu');
        $response = $loanResource->retryPaymentLoan(1);

        $this->assertIsArray($response);
        $this->assertArrayHasKey('response', $response);
        $this->assertEquals('Good job', $response['response']);
    }

    public function testRetryPaymentLoanReturnsError()
    {
        $response = new MockResponse(json_encode([
            'status' => '400',
            'message' => 'Something\'s wrong',
            'code' => 'BAD_REQUEST',
        ]));
        $httpClient = new MockHttpClient($response, 'https://example.com');
        $bearerGenerator = $this->createMock(BearerGenerator::class);

        $loanResource = new LoanResource($httpClient, $bearerGenerator, 'https://api.sandbox.helloaria.eu');
        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage('Error 400 BAD_REQUEST: Something\'s wrong');
        $loanResource->retryPaymentLoan(1);
    }

    public function testCancelLoanRequest()
    {
        $response = new MockResponse(json_encode(['response' => 'Good job']));
        $httpClient = new MockHttpClient($response, 'https://example.com');
        $bearerGenerator = $this->createMock(BearerGenerator::class);

        $loanResource = new LoanResource($httpClient, $bearerGenerator, 'https://api.sandbox.helloaria.eu');
        $response = $loanResource->cancelLoan(1);

        $this->assertIsArray($response);
        $this->assertArrayHasKey('response', $response);
        $this->assertEquals('Good job', $response['response']);
    }

    public function testCancelLoanReturnsError()
    {
        $response = new MockResponse(json_encode([
            'status' => '400',
            'message' => 'Something\'s wrong',
            'code' => 'BAD_REQUEST',
        ]));
        $httpClient = new MockHttpClient($response, 'https://example.com');
        $bearerGenerator = $this->createMock(BearerGenerator::class);

        $loanResource = new LoanResource($httpClient, $bearerGenerator, 'https://api.sandbox.helloaria.eu');
        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage('Error 400 BAD_REQUEST: Something\'s wrong');
        $loanResource->cancelLoan(1);
    }

    public function testDeleteLoanRequest()
    {
        $response = new MockResponse(json_encode(['response' => 'Good job']));
        $httpClient = new MockHttpClient($response, 'https://example.com');
        $bearerGenerator = $this->createMock(BearerGenerator::class);

        $loanResource = new LoanResource($httpClient, $bearerGenerator, 'https://api.sandbox.helloaria.eu');
        $response = $loanResource->deleteLoan(1);

        $this->assertIsArray($response);
        $this->assertArrayHasKey('response', $response);
        $this->assertEquals('Good job', $response['response']);
    }

    public function testDeleteLoanReturnsError()
    {
        $response = new MockResponse(json_encode([
            'status' => '400',
            'message' => 'Something\'s wrong',
            'code' => 'BAD_REQUEST',
        ]));
        $httpClient = new MockHttpClient($response, 'https://example.com');
        $bearerGenerator = $this->createMock(BearerGenerator::class);

        $loanResource = new LoanResource($httpClient, $bearerGenerator, 'https://api.sandbox.helloaria.eu');
        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage('Error 400 BAD_REQUEST: Something\'s wrong');
        $loanResource->deleteLoan(1);
    }

    private function getLoan(): array
    {
        return [
            'amount' => 100,
            'currency' => 'EUR',
            'userId' => '455cba4c-1db3-4ead-8f4c-a860a2692e6a',
            'invoiceId' => 'b55f430f-59e7-40e9-aef9-a2b780fd968f',
            'paymentDestination' => ['accountNumber' => 'FR7630001007941234567890185'],
            'externalId' => 'MED-12345',
            'quoteId' => null,
            'payoutLabel' => 'Cash for loan MED-12345',
            'attachments' => [['url' => 'https://sample.com']],
            'guarantor' => null,
            'preferInstantPayment' => null,
        ];
    }

    private function getLoanDataForUpdate(): array
    {
        return [
            'amount' => 110,
            'invoiceId' => 'b55f430f-59e7-40e9-aef9-a2b780fd968b',
            'paymentDestination' => ['accountNumber' => 'FR7630001007941234567890186'],
        ];
    }
}
