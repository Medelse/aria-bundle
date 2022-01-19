<?php

namespace Medelse\AriaBundle\Tests\Resolver\User;

use Medelse\AriaBundle\Resolver\User\UpdateUserResolver;
use PHPUnit\Framework\TestCase;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;

class UpdateUserResolverTest extends TestCase
{
    public function testResolveWithOneValueReturnArrayWithOneKey()
    {
        $userData = [
            'phone' => '911',
        ];
        $resolver = new UpdateUserResolver();
        $ariaData = $resolver->resolve($userData);

        $this->assertIsArray($ariaData);
        $this->assertCount(1, $ariaData);
        $this->assertArrayHasKey('phone', $ariaData);
        $this->assertEquals('911', $ariaData['phone']);
    }

    public function testResolveWithTwoValuesReturnArrayWithTwoKeys()
    {
        $userData = [
            'email' => 'sidney.prescott@mail.com',
            'givenName' => 'Sidney',
        ];
        $resolver = new UpdateUserResolver();
        $ariaData = $resolver->resolve($userData);

        $this->assertIsArray($ariaData);
        $this->assertCount(2, $ariaData);
        $this->assertArrayHasKey('email', $ariaData);
        $this->assertEquals('sidney.prescott@mail.com', $ariaData['email']);
        $this->assertArrayHasKey('givenName', $ariaData);
        $this->assertEquals('Sidney', $ariaData['givenName']);
    }

    public function testUpdateWithOnlyAddressFirstValueThrowsException()
    {
        $userData = [
            'addressFirst' => '261 Turner Lane',
        ];

        $resolver = new UpdateUserResolver();
        $this->expectException(MissingOptionsException::class);
        $this->expectExceptionMessage('Options addressCity, addressPostal, addressCountry are required to update the addressFirst');
        $resolver->resolve($userData);
    }

    public function testUpdateWithOnlyAddressSecondValueThrowsException()
    {
        $userData = [
            'addressSecond' => 'Turner Lane',
        ];

        $resolver = new UpdateUserResolver();
        $this->expectException(MissingOptionsException::class);
        $this->expectExceptionMessage('Options addressFirst, addressCity, addressPostal, addressCountry are required to update the address');
        $resolver->resolve($userData);
    }

    public function testUpdateWithOnlyAddressCityValueThrowsException()
    {
        $userData = [
            'addressCity' => 'Woodsboro',
        ];

        $resolver = new UpdateUserResolver();
        $this->expectException(MissingOptionsException::class);
        $this->expectExceptionMessage('Options addressFirst, addressPostal, addressCountry are required to update the addressCity');
        $resolver->resolve($userData);
    }

    public function testUpdateWithOnlyAddressRegionValueThrowsException()
    {
        $userData = [
            'addressRegion' => 'California',
        ];

        $resolver = new UpdateUserResolver();
        $this->expectException(MissingOptionsException::class);
        $this->expectExceptionMessage('Options addressFirst, addressCity, addressPostal, addressCountry are required to update the address');
        $resolver->resolve($userData);
    }

    public function testUpdateWithOnlyAddressPostalValueThrowsException()
    {
        $userData = [
            'addressPostal' => '90011',
        ];

        $resolver = new UpdateUserResolver();
        $this->expectException(MissingOptionsException::class);
        $this->expectExceptionMessage('Options addressFirst, addressCity, addressCountry are required to update the addressPostal');
        $resolver->resolve($userData);
    }

    public function testUpdateWithOnlyAddressCountryValueThrowsException()
    {
        $userData = [
            'addressCountry' => 'fr',
        ];

        $resolver = new UpdateUserResolver();
        $this->expectException(MissingOptionsException::class);
        $this->expectExceptionMessage('Options addressFirst, addressCity, addressPostal are required to update the addressCountry');
        $resolver->resolve($userData);
    }

    public function testUpdateAddressWithOnlyRequiredValues()
    {
        $userData = [
            'addressFirst' => '261 Turner Lane',
            'addressCity' => 'Woodsboro',
            'addressPostal' => '90011',
            'addressCountry' => 'usa',
        ];
        $resolver = new UpdateUserResolver();
        $ariaData = $resolver->resolve($userData);

        $this->assertIsArray($ariaData);
        $this->assertCount(1, $ariaData);
        $this->assertArrayHasKey('address', $ariaData);

        $this->assertIsArray($ariaData['address']);
        $this->assertCount(6, $ariaData['address']);

        $this->assertArrayHasKey('first', $ariaData['address']);
        $this->assertEquals('261 Turner Lane', $ariaData['address']['first']);

        $this->assertArrayHasKey('second', $ariaData['address']);
        $this->assertNull($ariaData['address']['second']);

        $this->assertArrayHasKey('city', $ariaData['address']);
        $this->assertEquals('Woodsboro', $ariaData['address']['city']);

        $this->assertArrayHasKey('region', $ariaData['address']);
        $this->assertNull($ariaData['address']['region']);

        $this->assertArrayHasKey('postal', $ariaData['address']);
        $this->assertEquals('90011', $ariaData['address']['postal']);

        $this->assertArrayHasKey('country', $ariaData['address']);
        $this->assertEquals('usa', $ariaData['address']['country']);
    }

    public function testUpdateAddressWithAllValues()
    {
        $userData = [
            'addressFirst' => '261 Turner Lane',
            'addressSecond' => 'Turner Lane',
            'addressCity' => 'Woodsboro',
            'addressRegion' => 'California',
            'addressPostal' => '90011',
            'addressCountry' => 'usa',
        ];
        $resolver = new UpdateUserResolver();
        $ariaData = $resolver->resolve($userData);

        $this->assertIsArray($ariaData);
        $this->assertCount(1, $ariaData);
        $this->assertArrayHasKey('address', $ariaData);

        $this->assertIsArray($ariaData['address']);
        $this->assertCount(6, $ariaData['address']);

        $this->assertArrayHasKey('first', $ariaData['address']);
        $this->assertEquals('261 Turner Lane', $ariaData['address']['first']);

        $this->assertArrayHasKey('second', $ariaData['address']);
        $this->assertEquals('Turner Lane', $ariaData['address']['second']);

        $this->assertArrayHasKey('city', $ariaData['address']);
        $this->assertEquals('Woodsboro', $ariaData['address']['city']);

        $this->assertArrayHasKey('region', $ariaData['address']);
        $this->assertEquals('California', $ariaData['address']['region']);

        $this->assertArrayHasKey('postal', $ariaData['address']);
        $this->assertEquals('90011', $ariaData['address']['postal']);

        $this->assertArrayHasKey('country', $ariaData['address']);
        $this->assertEquals('usa', $ariaData['address']['country']);
    }
}
