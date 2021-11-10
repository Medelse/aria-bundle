<?php

namespace Medelse\AriaBundle\Tests\Resource;

use Medelse\AriaBundle\Resource\User as UserResource;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class UserTest extends TestCase
{
    public function testCreateUser()
    {
        $response = new MockResponse(json_encode(['response' => 'Good job']));
        $httpClient = new MockHttpClient($response, 'https://example.com');

        $userResource = new UserResource($httpClient, '', 'ariaApiKey');
        $response = $userResource->createUser($this->getUser());

        $this->assertIsArray($response);
        $this->assertArrayHasKey('response', $response);
        $this->assertEquals('Good job', $response['response']);
    }

    public function testCreateUserReturnsError()
    {
        $response = new MockResponse(json_encode([
            'status' => '400',
            'message' => 'Something\'s wrong',
            'code' => 'BAD_REQUEST',
        ]));
        $httpClient = new MockHttpClient($response, 'https://example.com');

        $userResource = new UserResource($httpClient, '', 'ariaApiKey');
        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage('Error 400 BAD_REQUEST: Something\'s wrong');
        $userResource->createUser($this->getUser());
    }

    public function testGetUserRequest()
    {
        $response = new MockResponse(json_encode(['response' => 'Good job']));
        $httpClient = new MockHttpClient($response, 'https://example.com');

        $userResource = new UserResource($httpClient, '', 'ariaApiKey');
        $response = $userResource->getUser(1);

        $this->assertIsArray($response);
        $this->assertArrayHasKey('response', $response);
        $this->assertEquals('Good job', $response['response']);
    }

    public function testGetUserReturnsError()
    {
        $response = new MockResponse(json_encode([
            'status' => '400',
            'message' => 'Something\'s wrong',
            'code' => 'BAD_REQUEST',
        ]));
        $httpClient = new MockHttpClient($response, 'https://example.com');

        $userResource = new UserResource($httpClient, '', 'ariaApiKey');
        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage('Error 400 BAD_REQUEST: Something\'s wrong');
        $userResource->getUser(1);
    }

    public function testSendUserContractRequest()
    {
        $response = new MockResponse(json_encode(['id' => 1, 'email' => 'mail@mail.fr']));
        $httpClient = new MockHttpClient($response, 'https://example.com');

        $userResource = new UserResource($httpClient, '', 'ariaApiKey');
        $response = $userResource->sendUserContract(1);

        $this->assertIsArray($response);
        $this->assertEquals(['id' => 1, 'email' => 'mail@mail.fr'], $response);
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
