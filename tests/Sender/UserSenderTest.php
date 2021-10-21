<?php

namespace Medelse\AriaBundle\Tests\Sender;

use Medelse\AriaBundle\Sender\UserSender;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class UserSenderTest extends TestCase
{
    public function testSendCreateUserRequest()
    {
        $response = new MockResponse(json_encode(['response' => 'Good job']));
        $httpClient = new MockHttpClient($response, 'https://example.com');

        $sender = new UserSender($httpClient, '', 'ariaApiKey');
        $response = $sender->createUser($this->getUser());

        $this->assertIsArray($response);
        $this->assertArrayHasKey('response', $response);
        $this->assertEquals('Good job', $response['response']);
    }

    public function testSendCreateUserRequestReturnsError()
    {
        $response = new MockResponse(json_encode([
            'status' => '400',
            'message' => 'Something\'s wrong',
            'code' => 'BAD_REQUEST',
        ]));
        $httpClient = new MockHttpClient($response, 'https://example.com');

        $sender = new UserSender($httpClient, '', 'ariaApiKey');
        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage('Error 400 BAD_REQUEST: Something\'s wrong');
        $sender->createUser($this->getUser());
    }

    /**
     *
     * PRIVATE
     *
     */

    private function getUser(): array
    {
        return [
            'phone' => '0909090909',
            'email' => 'zombieland@aria.com',
            'givenName' => 'Bill',
            'familyName' => 'Murray',
            'addressFirst' => 'Pacific Playland',
            'addressSecond' => '',
            'addressCity' => 'Austin',
            'addressRegion' => '',
            'addressPostal' => '05000',
            'addressCountry' => 'zomb-land',
            'siren' => '123456789',
            'businessName' => 'My spooky company',
            'bankAccountIBAN' => 'FR14 3000 1019 0100 00Z6 7067 032',
            'bankAccountBIC' => 'DAEEFRPPCCT',
        ];
    }
}
