<?php

namespace Medelse\AriaBundle\Tests\Resolver\Invoice;

use Medelse\AriaBundle\Resolver\Invoice\UpdateInvoiceResolver;
use PHPUnit\Framework\TestCase;

class UpdateInvoiceResolverTest extends TestCase
{
    /**
     * @dataProvider invoiceDataProvider
     */
    public function testResolve(array $data, int $assertLength)
    {
        $resolver = new UpdateInvoiceResolver();
        $data = $resolver->resolve($data);

        $this->assertIsArray($data);
        $this->assertSame($assertLength, count($data));
    }

    public function invoiceDataProvider(): array
    {
        return [
            [
                [
                    'attachments' => [['url' => 'https://sample.com']],
                    'totalAmountIncludingTaxes' => 100,
                ],
                2,
            ],
            [
                [
                    'attachments' => [['url' => 'https://sample.com']],
                    'totalAmountIncludingTaxes' => 100,
                    'totalAmountExcludingTaxes' => 90,
                    'currency' => 'EUR',
                ],
                4,
            ],
            [
                [
                    'debtorIdentifier' => ['type' => 'siren', 'value' => '999555999'],
                    'contacts' => [
                        ['email' => 'test@sample.com', 'phoneNumber' => '+33606060606'],
                        ['email' => 'test@sample.com', 'phoneNumber' => '+33606060606'],
                    ],
                ],
                2,
            ],
        ];
    }
}
