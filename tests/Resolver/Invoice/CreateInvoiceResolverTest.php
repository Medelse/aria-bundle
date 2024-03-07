<?php

namespace Medelse\AriaBundle\Tests\Resolver\Invoice;

use Medelse\AriaBundle\Resolver\Invoice\CreateInvoiceResolver;
use PHPUnit\Framework\TestCase;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;

class CreateInvoiceResolverTest extends TestCase
{
    /**
     * @dataProvider invoiceDataWithErrorsProvider
     */
    public function testResolveWithErrors(array $data, string $exceptionClass, string $message)
    {
        $resolver = new CreateInvoiceResolver();

        $this->expectException($exceptionClass);
        $this->expectExceptionMessage($message);
        $resolver->resolve($data);
    }

    public function testResolve()
    {
        $date = (new \DateTime())->setTime(0, 0, 0);
        $resolver = new CreateInvoiceResolver();
        $data = $resolver->resolve($this->getInvoice($date));

        $this->assertIsArray($data);
        $this->assertSame(10, count($data));
        $this->assertEquals($date->format(\DateTimeInterface::ATOM), $data['invoiceDate']);
    }

    public function invoiceDataWithErrorsProvider(): array
    {
        return [
            [
                [],
                MissingOptionsException::class,
                'The required options "attachments", "debtorIdentifier", "debtorName", "invoiceDate", "invoiceNumber", "repaymentPeriod", "totalAmountExcludingTaxes", "totalAmountIncludingTaxes" are missing',
            ],
            [
                ['debtorId' => '12'],
                MissingOptionsException::class,
                'The required options "attachments", "debtorIdentifier", "debtorName", "invoiceDate", "invoiceNumber", "repaymentPeriod", "totalAmountExcludingTaxes", "totalAmountIncludingTaxes" are missing',
            ],
            [
                [
                    'attachments' => [],
                ],
                MissingOptionsException::class,
                'The required options "debtorIdentifier", "debtorName", "invoiceDate", "invoiceNumber", "repaymentPeriod", "totalAmountExcludingTaxes", "totalAmountIncludingTaxes" are missing',
            ],
            [
                [
                    'attachments' => [],
                    'totalAmountIncludingTaxes' => 10,
                ],
                MissingOptionsException::class,
                'The required options "debtorIdentifier", "debtorName", "invoiceDate", "invoiceNumber", "repaymentPeriod", "totalAmountExcludingTaxes" are missing',
            ],
            [
                [
                    'attachments' => [],
                    'totalAmountIncludingTaxes' => 10,
                    'totalAmountExcludingTaxes' => 10,
                ],
                MissingOptionsException::class,
                'The required options "debtorIdentifier", "debtorName", "invoiceDate", "invoiceNumber", "repaymentPeriod" are missing',
            ],
            [
                [
                    'attachments' => [],
                    'totalAmountIncludingTaxes' => 10,
                    'totalAmountExcludingTaxes' => 10,
                    'invoiceDate' => new \DateTime(),
                ],
                MissingOptionsException::class,
                'The required options "debtorIdentifier", "debtorName", "invoiceNumber", "repaymentPeriod" are missing',
            ],
            [
                [
                    'attachments' => [],
                    'totalAmountIncludingTaxes' => 10,
                    'totalAmountExcludingTaxes' => 10,
                    'invoiceDate' => new \DateTime(),
                    'invoiceNumber' => 'INV_0123',
                ],
                MissingOptionsException::class,
                'The required options "debtorIdentifier", "debtorName", "repaymentPeriod" are missing',
            ],
            [
                [
                    'attachments' => [],
                    'totalAmountIncludingTaxes' => 10,
                    'totalAmountExcludingTaxes' => 10,
                    'invoiceDate' => new \DateTime(),
                    'invoiceNumber' => 'INV_0123',
                    'debtorName' => 'Lorem',
                ],
                MissingOptionsException::class,
                'The required options "debtorIdentifier", "repaymentPeriod" are missing',
            ],
            [
                [
                    'attachments' => [],
                    'totalAmountIncludingTaxes' => 10,
                    'totalAmountExcludingTaxes' => 10,
                    'invoiceDate' => new \DateTime(),
                    'invoiceNumber' => 'INV_0123',
                    'debtorName' => 'Lorem',
                    'debtorIdentifier' => [],
                    'repaymentPeriod' => 30,
                ],
                InvalidOptionsException::class,
                'The option "attachments" cannot be empty',
            ],
            [
                [
                    'attachments' => [['type' => 'invoice']],
                    'totalAmountIncludingTaxes' => 10,
                    'totalAmountExcludingTaxes' => 10,
                    'invoiceDate' => new \DateTime(),
                    'invoiceNumber' => 'INV_0123',
                    'debtorName' => 'Lorem',
                    'debtorIdentifier' => [],
                    'repaymentPeriod' => 30,
                ],
                MissingOptionsException::class,
                'The required option "url" is missing',
            ],
            [
                [
                    'attachments' => [['url' => 'sample.com']],
                    'totalAmountIncludingTaxes' => 10,
                    'totalAmountExcludingTaxes' => 10,
                    'invoiceDate' => new \DateTime(),
                    'invoiceNumber' => 'INV_0123',
                    'debtorName' => 'Lorem',
                    'debtorIdentifier' => [],
                    'repaymentPeriod' => 30,
                ],
                InvalidOptionsException::class,
                'The option "url" with value "sample.com" is invalid',
            ],
            [
                [
                    'attachments' => [['url' => 'http://sample.com']],
                    'totalAmountIncludingTaxes' => 10,
                    'totalAmountExcludingTaxes' => 10,
                    'invoiceDate' => new \DateTime(),
                    'invoiceNumber' => 'INV_0123',
                    'debtorName' => 'Lorem',
                    'debtorIdentifier' => ['type' => 'test'],
                    'repaymentPeriod' => 30,
                ],
                MissingOptionsException::class,
                'The required option "value" is missing',
            ],
            [
                [
                    'attachments' => [['url' => 'http://sample.com']],
                    'totalAmountIncludingTaxes' => 10,
                    'totalAmountExcludingTaxes' => 10,
                    'invoiceDate' => new \DateTime(),
                    'invoiceNumber' => 'INV_0123',
                    'debtorName' => 'Lorem',
                    'debtorIdentifier' => ['type' => 'test', 'value' => 'test'],
                    'repaymentPeriod' => 30,
                ],
                InvalidOptionsException::class,
                'The option "type" with value "test" is invalid',
            ],
            [
                [
                    'attachments' => [['url' => 'http://sample.com']],
                    'totalAmountIncludingTaxes' => 10,
                    'totalAmountExcludingTaxes' => 10,
                    'invoiceDate' => new \DateTime(),
                    'invoiceNumber' => 'INV_0123',
                    'debtorName' => 'Lorem',
                    'debtorIdentifier' => ['country' => 'test', 'value' => 'test'],
                    'repaymentPeriod' => 30,
                ],
                InvalidOptionsException::class,
                'The option "country" with value "test" is invalid',
            ],
            [
                [
                    'attachments' => [['url' => 'http://sample.com']],
                    'totalAmountIncludingTaxes' => 10,
                    'totalAmountExcludingTaxes' => 10,
                    'invoiceDate' => new \DateTime(),
                    'invoiceNumber' => 'INV_0123',
                    'debtorName' => 'Lorem',
                    'debtorIdentifier' => ['value' => 'test'],
                    'repaymentPeriod' => 30,
                    'contacts' => [['email' => 'test']],
                ],
                MissingOptionsException::class,
                'The required option "phoneNumber" is missing',
            ],
            [
                [
                    'attachments' => [['url' => 'http://sample.com']],
                    'totalAmountIncludingTaxes' => 10,
                    'totalAmountExcludingTaxes' => 10,
                    'invoiceDate' => new \DateTime(),
                    'invoiceNumber' => 'INV_0123',
                    'debtorName' => 'Lorem',
                    'debtorIdentifier' => ['value' => 'test'],
                    'repaymentPeriod' => 30,
                    'contacts' => [['email' => 'test', 'phoneNumber' => '0606060606']],
                ],
                InvalidOptionsException::class,
                'The option "email" with value "test" is invalid',
            ],
            [
                [
                    'attachments' => [['url' => 'http://sample.com']],
                    'totalAmountIncludingTaxes' => 10,
                    'totalAmountExcludingTaxes' => 10,
                    'invoiceDate' => new \DateTime(),
                    'invoiceNumber' => 'INV_0123',
                    'debtorName' => 'Lorem',
                    'debtorIdentifier' => ['value' => 'test'],
                    'repaymentPeriod' => 30,
                    'contacts' => [['email' => 'test@sample.com', 'phoneNumber' => '0606060606']],
                ],
                InvalidOptionsException::class,
                'The option "phoneNumber" with value "0606060606" is invalid',
            ],
        ];
    }

    private function getInvoice(\DateTime $date): array
    {
        return [
            'debtorId' => null,
            'attachments' => [['url' => 'https://sample.com']],
            'totalAmountIncludingTaxes' => 100,
            'totalAmountExcludingTaxes' => 90,
            'currency' => 'EUR',
            'invoiceDate' => $date,
            'invoiceNumber' => '12340',
            'invoiceOwner' => null,
            'debtorName' => 'Leeroy Jenkins',
            'debtorIdentifier' => ['type' => 'siren', 'value' => '999555999'],
            'contacts' => [['email' => 'test@sample.com', 'phoneNumber' => '+33606060606']],
            'debtorMetadata' => null,
            'repaymentPeriod' => 30,
        ];
    }
}
