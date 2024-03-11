<?php

namespace Medelse\AriaBundle\Tests\Resolver\Loan;

use Medelse\AriaBundle\Resolver\Loan\UpdateLoanResolver;
use PHPUnit\Framework\TestCase;

class UpdateLoanResolverTest extends TestCase
{
    /**
     * @dataProvider loanDataProvider
     */
    public function testResolve(array $data, int $assertLength)
    {
        $resolver = new UpdateLoanResolver();
        $data = $resolver->resolve($data);

        $this->assertIsArray($data);
        $this->assertSame($assertLength, count($data));
    }

    public function loanDataProvider(): array
    {
        return [
            [
                [
                    'amount' => 100,
                    'attachments' => [['url' => 'https://sample.com']],
                ],
                2,
            ],
            [
                [
                    'amount' => 100,
                    'attachments' => [['url' => 'https://sample.com']],
                    'currency' => 'EUR',
                ],
                3,
            ],
            [
                [
                    'userId' => '455cba4c-1db3-4ead-8f4c-a860a2692e6a',
                    'invoiceId' => 'b55f430f-59e7-40e9-aef9-a2b780fd968f',
                    'paymentDestination' => ['accountNumber' => 'FR7630001007941234567890185'],
                    'payoutLabel' => 'Cash for invoice MED-12345',
                ],
                4,
            ],
        ];
    }
}
