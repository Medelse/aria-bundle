<?php

namespace Medelse\AriaBundle\Resolver\Invoice;

use Medelse\AriaBundle\Enum\CurrencyEnum;
use Medelse\AriaBundle\Tool\ArrayFormatter;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UpdateInvoiceResolver extends CreateInvoiceResolver
{
    public function resolve(array $data): array
    {
        $resolver = new OptionsResolver();
        $this->configureOptionsResolver($resolver);
        $data = $resolver->resolve($data);

        return ArrayFormatter::removeNullValues($data);
    }

    private function configureOptionsResolver(OptionsResolver $resolver): void
    {
        $resolver->setDefined([
            'debtorId',
            'attachments',
            'totalAmountIncludingTaxes',
            'totalAmountExcludingTaxes',
            'currency',
            'invoiceDate',
            'invoiceNumber',
            'invoiceOwner',
            'debtorName',
            'debtorIdentifier',
            'contacts',
            'debtorMetadata',
            'repaymentPeriod',
        ]);

        $resolver
            ->setAllowedTypes('debtorId', ['null', 'string'])
            ->setAllowedTypes('attachments', ['null', 'array'])
            ->setAllowedValues('attachments', function (&$data) {
                if (!empty($data)) {
                    foreach ($data as $index => $attachment) {
                        $resolver = new OptionsResolver();
                        $this->configureAttachmentOptionsResolver($resolver);
                        $data[$index] = $resolver->resolve($attachment);
                    }
                }

                return true;
            })
            ->setAllowedTypes('totalAmountIncludingTaxes', ['null', 'numeric'])
            ->setAllowedValues('totalAmountIncludingTaxes', function ($value) {
                return is_null($value) || $value > 0;
            })
            ->setAllowedTypes('totalAmountExcludingTaxes', ['null', 'numeric'])
            ->setAllowedValues('totalAmountExcludingTaxes', function ($value) {
                return is_null($value) || $value > 0;
            })
            ->setAllowedTypes('currency', ['null', 'string'])
            ->setAllowedValues('currency', function ($value) {
                return is_null($value) || in_array($value, CurrencyEnum::CURRENCIES);
            })
            ->setAllowedTypes('invoiceDate', ['null', \DateTimeInterface::class])
            ->setNormalizer('invoiceDate', function (Options $options, $value) {
                return is_null($value) ? null : $value->format(\DateTimeInterface::ATOM);
            })
            ->setAllowedTypes('invoiceNumber', ['null', 'string', 'numeric'])
            ->setNormalizer('invoiceNumber', function (Options $options, $value) {
                return is_null($value) ? null : (string) $value;
            })
            ->setAllowedTypes('invoiceOwner', ['null', 'string'])
            ->setAllowedValues('invoiceOwner', function ($value) {
                return is_null($value) || in_array($value, self::OWNERS);
            })
            ->setAllowedTypes('debtorName', ['null', 'string'])
            ->setAllowedTypes('debtorIdentifier', ['null', 'array'])
            ->setAllowedValues('debtorIdentifier', function (&$data) {
                if (!empty($data)) {
                    $resolver = new OptionsResolver();
                    $this->configureDebtorIdentifierOptionsResolver($resolver);
                    $data = $resolver->resolve($data);
                }

                return true;
            })
            ->setAllowedTypes('contacts', ['null', 'array'])
            ->setAllowedValues('contacts', function (&$data) {
                if (!empty($data)) {
                    foreach ($data as $index => $contact) {
                        $resolver = new OptionsResolver();
                        $this->configureContactOptionsResolver($resolver);
                        $data[$index] = $resolver->resolve($contact);
                    }
                }

                return true;
            })
            ->setAllowedTypes('debtorMetadata', ['null', 'array'])
            ->setAllowedTypes('repaymentPeriod', ['null', 'numeric'])
            ->setAllowedValues('repaymentPeriod', function ($value) {
                return is_null($value) || in_array($value, self::REPAYMENT_PERIODS);
            })
        ;
    }
}
