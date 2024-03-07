<?php

namespace Medelse\AriaBundle\Resolver\Invoice;

use Medelse\AriaBundle\Tool\ArrayFormatter;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateInvoiceResolver
{
    public const CURRENCY_EUR = 'EUR';
    public const CURRENCY_GBP = 'GBP';
    public const CURRENCY_USD = 'USD';
    public const CURRENCIES = [
        self::CURRENCY_EUR,
        self::CURRENCY_GBP,
        self::CURRENCY_USD,
    ];

    public const OWNER_USER = 'user';
    public const OWNER_COMPANY = 'company';
    public const OWNERS = [
        self::OWNER_USER,
        self::OWNER_COMPANY,
    ];

    public const IDENTIFIER_TYPE_SIREN = 'siren';
    public const IDENTIFIER_COUNTRY_FR = 'FR';

    public const REPAYMENT_PERIOD_30 = 30;
    public const REPAYMENT_PERIOD_45 = 45;
    public const REPAYMENT_PERIOD_60 = 60;
    public const REPAYMENT_PERIODS = [
        self::REPAYMENT_PERIOD_30,
        self::REPAYMENT_PERIOD_45,
        self::REPAYMENT_PERIOD_60,
    ];


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

        $resolver->setRequired([
            'attachments',
            'totalAmountIncludingTaxes',
            'totalAmountExcludingTaxes',
            'currency',
            'invoiceDate',
            'invoiceNumber',
            'debtorName',
            'debtorIdentifier',
            'repaymentPeriod',
        ]);

        $resolver->setDefaults([
            'currency' => self::CURRENCY_EUR,
        ]);

        $resolver
            ->setAllowedTypes('debtorId', ['null', 'string'])
            ->setAllowedTypes('attachments', ['array'])
            ->setAllowedValues('attachments', function (&$data) {
                if (empty($data)) {
                    throw new InvalidOptionsException('The option "attachments" cannot be empty');
                }

                foreach ($data as $index => $attachment) {
                    $resolver = new OptionsResolver();
                    $this->configureAttachmentOptionsResolver($resolver);
                    $data[$index] = $resolver->resolve($attachment);
                }

                return true;
            })
            ->setAllowedTypes('totalAmountIncludingTaxes', ['numeric'])
            ->setAllowedValues('totalAmountIncludingTaxes', function ($value) {
                return $value > 0;
            })
            ->setAllowedTypes('totalAmountExcludingTaxes', ['numeric'])
            ->setAllowedValues('totalAmountExcludingTaxes', function ($value) {
                return $value > 0;
            })
            ->setAllowedTypes('currency', ['string'])
            ->setAllowedValues('currency', function ($value) {
                return in_array($value, self::CURRENCIES);
            })
            ->setAllowedTypes('invoiceDate', [\DateTimeInterface::class])
            ->setNormalizer('invoiceDate', function (Options $options, $value) {
                return $value->format(\DateTimeInterface::ATOM);
            })
            ->setAllowedTypes('invoiceNumber', ['null', 'string', 'numeric'])
            ->setNormalizer('invoiceNumber', function (Options $options, $value) {
                return (string) $value;
            })
            ->setAllowedTypes('invoiceOwner', ['null', 'string'])
            ->setAllowedValues('invoiceOwner', function ($value) {
                return is_null($value) || in_array($value, self::OWNERS);
            })
            ->setAllowedTypes('debtorName', ['string'])
            ->setAllowedTypes('debtorIdentifier', ['array'])
            ->setAllowedValues('debtorIdentifier', function (&$data) {
                $resolver = new OptionsResolver();
                $this->configureDebtorIdentifierOptionsResolver($resolver);
                $data = $resolver->resolve($data);

                return true;
            })
            ->setAllowedTypes('contacts', ['array'])
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
            ->setAllowedTypes('repaymentPeriod', ['numeric'])
            ->setAllowedValues('repaymentPeriod', function ($value) {
                return in_array($value, self::REPAYMENT_PERIODS);
            })
        ;
    }

    protected function configureAttachmentOptionsResolver(OptionsResolver $resolver): void
    {
        $resolver->setDefined([
            'type',
            'url',
            'labels',
        ]);

        $resolver->setRequired([
            'url',
        ]);

        $resolver
            ->setAllowedTypes('type', ['null', 'string'])
            ->setAllowedTypes('url', ['string'])
            ->setAllowedValues('url', function ($value) {
                return filter_var($value, FILTER_VALIDATE_URL);
            })
            ->setAllowedTypes('labels', ['null', 'array'])
        ;
    }

    protected function configureDebtorIdentifierOptionsResolver(OptionsResolver $resolver): void
    {
        $resolver->setDefined([
            'type',
            'value',
            'country',
        ]);

        $resolver->setRequired([
            'type',
            'value',
            'country',
        ]);

        $resolver->setDefaults([
            'type' => self::IDENTIFIER_TYPE_SIREN,
            'country' => self::IDENTIFIER_COUNTRY_FR,
        ]);

        $resolver
            ->setAllowedTypes('type', ['string'])
            ->setAllowedValues('type', function ($value) {
                return in_array($value, [self::IDENTIFIER_TYPE_SIREN]);
            })
            ->setAllowedTypes('value', ['string'])
            ->setAllowedTypes('country', ['string'])
            ->setAllowedValues('country', function ($value) {
                return in_array($value, [self::IDENTIFIER_COUNTRY_FR]);
            })
        ;
    }

    protected function configureContactOptionsResolver(OptionsResolver $resolver): void
    {
        $resolver->setDefined([
            'email',
            'firstName',
            'lastName',
            'phoneNumber',
            'position',
        ]);

        $resolver->setRequired([
            'email',
            'phoneNumber',
        ]);

        $resolver
            ->setAllowedTypes('email', ['string'])
            ->setAllowedValues('email', function ($value) {
                return filter_var($value, FILTER_VALIDATE_EMAIL);
            })
            ->setAllowedTypes('firstName', ['null', 'string'])
            ->setAllowedTypes('lastName', ['null', 'string'])
            ->setAllowedTypes('phoneNumber', ['null', 'string'])
            ->setAllowedValues('phoneNumber', function ($value) {
                return preg_match('/^\+\d+$/', $value);
            })
            ->setAllowedTypes('position', ['null', 'string'])
        ;
    }
}
