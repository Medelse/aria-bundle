<?php

namespace Medelse\AriaBundle\Tests\Tool;

use Medelse\AriaBundle\Tool\ArrayFormatter;
use PHPUnit\Framework\TestCase;

class ArrayFormatterTest extends TestCase
{
    public function testRemoveNullValues()
    {
        $data = [
            'phone' => null,
            'email' => 'freddy@krueger.com',
            'givenName' => 'Freddy',
            'familyName' => null,
            'address' => [
                'first' => 'Elm Street',
                'second' => null,
                'city' => 'Springwood',
                'region' => '',
                'postal' => '43004',
                'country' => 'FR',
            ],
            'siren' => '123456789',
            'businessName' => 'Krueger Service',
            'bankAccount' => null,
        ];

        $data = ArrayFormatter::removeNullValues($data);

        $this->assertArrayNotHasKey('phone', $data);

        $this->assertArrayHasKey('email', $data);
        $this->assertEquals('freddy@krueger.com', $data['email']);

        $this->assertArrayHasKey('givenName', $data);
        $this->assertEquals('Freddy', $data['givenName']);

        $this->assertArrayNotHasKey('familyName', $data);

        $this->assertArrayHasKey('address', $data);
        $addressArray = $data['address'];
        $this->assertIsArray($addressArray);
        $this->assertArrayHasKey('first', $addressArray);
        $this->assertEquals('Elm Street', $addressArray['first']);
        $this->assertArrayNotHasKey('second', $addressArray);
        $this->assertArrayHasKey('city', $addressArray);
        $this->assertEquals('Springwood', $addressArray['city']);
        $this->assertArrayNotHasKey('region', $addressArray);
        $this->assertArrayHasKey('postal', $addressArray);
        $this->assertEquals('43004', $addressArray['postal']);
        $this->assertArrayHasKey('country', $addressArray);
        $this->assertEquals('FR', $addressArray['country']);

        $this->assertArrayHasKey('siren', $data);
        $this->assertEquals('123456789', $data['siren']);

        $this->assertArrayHasKey('businessName', $data);
        $this->assertEquals('Krueger Service', $data['businessName']);

        $this->assertArrayNotHasKey('bankAccount', $data);
    }
}
