<?php

namespace Medelse\AriaBundle\Tests\Resolver\User;

use Medelse\AriaBundle\Enum\BusinessTypeEnum;
use Medelse\AriaBundle\Resolver\User\CreateUserResolver;
use PHPUnit\Framework\TestCase;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;

class CreateUserResolverTest extends TestCase
{
    public function testResolve()
    {
        $user = $this->getUser();

        $resolver = new CreateUserResolver();
        $data = $resolver->resolve($user);

        $this->assertIsArray($data);
        $this->assertArrayHasKey('phone', $data);
        $this->assertEquals('0909090909', $data['phone']);
        $this->assertArrayHasKey('email', $data);
        $this->assertEquals('zombieland@aria.com', $data['email']);
        $this->assertArrayHasKey('givenName', $data);
        $this->assertEquals('Bill', $data['givenName']);
        $this->assertArrayHasKey('familyName', $data);
        $this->assertEquals('Murray', $data['familyName']);
        $this->assertArrayHasKey('siren', $data);
        $this->assertEquals('123456789', $data['siren']);
        $this->assertArrayHasKey('businessName', $data);
        $this->assertEquals('My spooky company', $data['businessName']);

        $this->assertArrayHasKey('address', $data);
        $this->assertIsArray($data['address']);
        $this->assertArrayHasKey('first', $data['address']);
        $this->assertEquals('Pacific Playland', $data['address']['first']);
        $this->assertArrayHasKey('city', $data['address']);
        $this->assertEquals('Austin', $data['address']['city']);
        $this->assertArrayHasKey('postal', $data['address']);
        $this->assertEquals('05000', $data['address']['postal']);
        $this->assertArrayHasKey('country', $data['address']);
        $this->assertEquals('US', $data['address']['country']);

        $this->assertArrayHasKey('bankAccount', $data);
        $this->assertIsArray($data['bankAccount']);
        $this->assertArrayHasKey('IBAN', $data['bankAccount']);
        $this->assertEquals('FR1430001019010000Z67067032', $data['bankAccount']['IBAN']);
        $this->assertArrayHasKey('BIC', $data['bankAccount']);
        $this->assertEquals('DAEEFRPPCCT', $data['bankAccount']['BIC']);
    }

    public function testBadSirenValue()
    {
        $user = $this->getUser();
        $user['siren'] = 'Little Rock';

        $resolver = new CreateUserResolver();
        $this->expectException(InvalidOptionsException::class);
        $resolver->resolve($user);
    }

    public function testSirenValueNormalizer()
    {
        $user = $this->getUser();
        $user['siren'] = '123 456 789';

        $resolver = new CreateUserResolver();
        $data = $resolver->resolve($user);

        $this->assertIsArray($data);
        $this->assertArrayHasKey('siren', $data);
        $this->assertEquals('123456789', $data['siren']);
    }

    public function testBankAccountBICValueEmpty()
    {
        $user = $this->getUser();
        $user['bankAccountBIC'] = null;

        $resolver = new CreateUserResolver();
        $data = $resolver->resolve($user);

        $this->assertIsArray($data);
        $this->assertArrayHasKey('bankAccount', $data);
        $this->assertArrayNotHasKey('BIC', $data['bankAccount']);
    }

    public function testBankAccountIBANWithLowerLettersAndSpaces()
    {
        $user = $this->getUser();
        $user['bankAccountBIC'] = 'fr14 3000 1019 0100 00z6 7067 032';

        $resolver = new CreateUserResolver();
        $data = $resolver->resolve($user);

        $this->assertIsArray($data);
        $this->assertArrayHasKey('bankAccount', $data);
        $this->assertArrayHasKey('IBAN', $data['bankAccount']);
        $this->assertEquals('FR1430001019010000Z67067032', $data['bankAccount']['IBAN']);
    }

    public function testBadEmailValue()
    {
        $user = $this->getUser();
        $user['addressCountry'] = 'zomb-land';

        $resolver = new CreateUserResolver();
        $this->expectException(InvalidOptionsException::class);
        $resolver->resolve($user);
    }

    public function testBadAddressCountryValue()
    {
        $user = $this->getUser();
        $user['email'] = 'this_is_not_a_valid_email';

        $resolver = new CreateUserResolver();
        $this->expectException(InvalidOptionsException::class);
        $resolver->resolve($user);
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
            'addressCountry' => 'us',
            'siren' => '123456789',
            'businessName' => 'My spooky company',
            'bankAccountIBAN' => 'FR14 3000 1019 0100 00Z6 7067 032',
            'bankAccountBIC' => 'DAEEFRPPCCT',
            'businessType' => BusinessTypeEnum::INDIVIDUAL,
        ];
    }
}
