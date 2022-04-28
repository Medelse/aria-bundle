<?php

namespace Medelse\AriaBundle\Tests\Resolver\Advance;

use Medelse\AriaBundle\Resolver\Advance\CreateAdvanceResolver;
use Medelse\AriaBundle\Resolver\User\CreateUserResolver;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;

class CreateAdvanceResolverTest extends TestCase
{
    public function testResolve()
    {
        $date = (new \DateTime())->setTime(0, 0, 0);
        $advance = $this->getAdvance($date);

        $resolver = new CreateAdvanceResolver();
        $data = $resolver->resolve($advance);

        $this->assertIsArray($data);
        $this->assertArrayHasKey('billDate', $data);
        $this->assertEquals($date->format(\DateTimeInterface::ISO8601), $data['billDate']);
        $this->assertArrayHasKey('amount', $data);
        $this->assertArrayHasKey('currency', $data);
        $this->assertArrayHasKey('duration', $data);
        $this->assertArrayHasKey('customerName', $data);
        $this->assertArrayHasKey('customerSiren', $data);
        $this->assertArrayHasKey('invoiceNumber', $data);
        $this->assertArrayHasKey('label', $data);
        $this->assertIsArray($data[0]);
        $this->assertArrayHasKey('bill', $data[0]);
        $this->assertTrue($data[0]['bill'] instanceof DataPart);
        $this->assertIsArray($data[1]);
        $this->assertArrayHasKey('bill', $data[1]);
        $this->assertTrue($data[1]['bill'] instanceof DataPart);
    }

    public function testEmptyBillValue()
    {
        $date = (new \DateTime())->setTime(0, 0, 0);
        $advance = $this->getAdvance($date);
        $advance['bill'] = [];

        $resolver = new CreateAdvanceResolver();
        $this->expectException(InvalidOptionsException::class);
        $this->expectExceptionMessage('Option "bill" cannot be empty');
        $resolver->resolve($advance);
    }

    public function testBillValueWithBadType()
    {
        $date = (new \DateTime())->setTime(0, 0, 0);
        $advance = $this->getAdvance($date);
        $advance['bill'] = ['Dr Frederick Chilton'];

        $resolver = new CreateAdvanceResolver();
        $this->expectException(InvalidOptionsException::class);
        $this->expectExceptionMessage('Option "bill" must be an array and have document and fileName keys');
        $resolver->resolve($advance);
    }

    public function testBillValueWithWrongKeys()
    {
        $date = (new \DateTime())->setTime(0, 0, 0);
        $advance = $this->getAdvance($date);
        $advance['bill'] = [['serial_killer' => 'Buffalo Bill']];

        $resolver = new CreateAdvanceResolver();
        $this->expectException(InvalidOptionsException::class);
        $this->expectExceptionMessage('Option "bill" must be an array and have document and fileName keys');
        $resolver->resolve($advance);
    }

    public function testBadAmountValue()
    {
        $date = (new \DateTime())->setTime(0, 0, 0);
        $advance = $this->getAdvance($date);
        $advance['amount'] = -150;

        $resolver = new CreateAdvanceResolver();
        $this->expectException(InvalidOptionsException::class);
        $resolver->resolve($advance);
    }

    public function testBadSirenValue()
    {
        $date = (new \DateTime())->setTime(0, 0, 0);
        $advance = $this->getAdvance($date);
        $advance['customerSiren'] = 'Little Rock';

        $resolver = new CreateAdvanceResolver();
        $this->expectException(InvalidOptionsException::class);
        $resolver->resolve($advance);
    }

    public function testSirenValueNormalizer()
    {
        $date = (new \DateTime())->setTime(0, 0, 0);
        $advance = $this->getAdvance($date);
        $advance['customerSiren'] = '123 456 789';

        $resolver = new CreateAdvanceResolver();
        $data = $resolver->resolve($advance);

        $this->assertIsArray($data);
        $this->assertArrayHasKey('customerSiren', $data);
        $this->assertEquals('123456789', $data['customerSiren']);
    }

    public function testBadCurrencyValue()
    {
        $date = (new \DateTime())->setTime(0, 0, 0);
        $advance = $this->getAdvance($date);
        $advance['currency'] = 'USD';

        $resolver = new CreateAdvanceResolver();
        $this->expectException(InvalidOptionsException::class);
        $resolver->resolve($advance);
    }

    /**
     *
     * PRIVATE
     *
     */

    private function getAdvance(\DateTime $date): array
    {
        return [
            'billDate' => $date,
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
