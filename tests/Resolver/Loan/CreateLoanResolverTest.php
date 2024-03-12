<?php

namespace Medelse\AriaBundle\Tests\Resolver\Loan;

use Medelse\AriaBundle\Resolver\Loan\CreateLoanResolver;
use PHPUnit\Framework\TestCase;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;

class CreateLoanResolverTest extends TestCase
{
    /**
     * @dataProvider loanDataWithErrorsProvider
     */
    public function testResolveWithErrors(array $data, string $exceptionClass, string $message)
    {
        $resolver = new CreateLoanResolver();

        $this->expectException($exceptionClass);
        $this->expectExceptionMessage($message);
        $resolver->resolve($data);
    }

    public function testResolve()
    {
        $resolver = new CreateLoanResolver();
        $data = $resolver->resolve($this->getLoan());

        $this->assertIsArray($data);
        $this->assertSame(8, count($data));
    }

    public function loanDataWithErrorsProvider(): array
    {
        return [
            [
                [],
                MissingOptionsException::class,
                'The required options "amount", "invoiceId", "paymentDestination", "userId" are missing',
            ],
            [
                ['externalId' => 'MED-12345'],
                MissingOptionsException::class,
                'The required options "amount", "invoiceId", "paymentDestination", "userId" are missing',
            ],
            [
                ['amount' => 100],
                MissingOptionsException::class,
                'The required options "invoiceId", "paymentDestination", "userId" are missing',
            ],
            [
                [
                    'amount' => 100,
                    'invoiceId' => 'b55f430f-59e7-40e9-aef9-a2b780fd968f',
                ],
                MissingOptionsException::class,
                'The required options "paymentDestination", "userId" are missing',
            ],
            [
                [
                    'amount' => 100,
                    'invoiceId' => 'b55f430f-59e7-40e9-aef9-a2b780fd968f',
                    'paymentDestination' => ['accountNumber' => 'FR7630001007941234567890185'],
                ],
                MissingOptionsException::class,
                'The required option "userId" is missing',
            ],
            [
                [
                    'amount' => 100,
                    'invoiceId' => 'b55f430f-59e7-40e9-aef9-a2b780fd968f',
                    'userId' => '455cba4c-1db3-4ead-8f4c-a860a2692e6a',
                ],
                MissingOptionsException::class,
                'The required option "paymentDestination" is missing',
            ],
            [
                [
                    'amount' => 100,
                    'invoiceId' => 'b55f430f-59e7-40e9-aef9-a2b780fd968f',
                    'paymentDestination' => ['routingNumberType' => '12345'],
                    'userId' => '455cba4c-1db3-4ead-8f4c-a860a2692e6a',
                ],
                MissingOptionsException::class,
                'The required option "accountNumber" is missing',
            ],
            [
                [
                    'amount' => 100,
                    'invoiceId' => 'b55f430f-59e7-40e9-aef9-a2b780fd968f',
                    'paymentDestination' => ['accountNumber' => 'FR7630001007941234567890185'],
                    'userId' => '455cba4c-1db3-4ead-8f4c-a860a2692e6a',
                    'attachments' => [['type' => 'invoice']],
                ],
                MissingOptionsException::class,
                'The required option "url" is missing',
            ],
            [
                [
                    'amount' => 100,
                    'invoiceId' => 'b55f430f-59e7-40e9-aef9-a2b780fd968f',
                    'paymentDestination' => ['accountNumber' => 'FR7630001007941234567890185'],
                    'userId' => '455cba4c-1db3-4ead-8f4c-a860a2692e6a',
                    'attachments' => [['url' => 'sample.com']],
                ],
                InvalidOptionsException::class,
                'The option "url" with value "sample.com" is invalid',
            ],
        ];
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
            'payoutLabel' => 'Cash for invoice MED-12345',
            'attachments' => [['url' => 'https://sample.com']],
            'guarantor' => null,
            'preferInstantPayment' => null,
        ];
    }
}
