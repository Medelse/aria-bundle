<?php

namespace Medelse\AriaBundle\Resolver\Loan;

use Medelse\AriaBundle\Enum\CurrencyEnum;
use Medelse\AriaBundle\Tool\ArrayFormatter;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateLoanResolver
{
    public const GUARANTOR_COMPANY = 'company';

    public const PAYMENT_DESTINATION_ACCOUNT_NUMBER_TYPE_IBAN = 'IBAN';
    public const PAYMENT_DESTINATION_TYPE_BANK_ACCOUNT = 'BANK_ACCOUNT';

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
            'amount',
            'currency',
            'userId',
            'invoiceId',
            'paymentDestination',
            'externalId',
            'quoteId',
            'payoutLabel',
            'attachments',
            'guarantor',
            'preferInstantPayment',
        ]);

        $resolver->setRequired([
            'amount',
            'currency',
            'userId',
            'invoiceId',
            'paymentDestination',
        ]);

        $resolver->setDefaults([
            'currency' => CurrencyEnum::CURRENCY_EUR,
            'preferInstantPayment' => false,
        ]);

        $resolver
            ->setAllowedTypes('amount', ['numeric'])
            ->setAllowedValues('amount', function ($value) {
                return $value > 0;
            })
            ->setAllowedTypes('currency', ['string'])
            ->setAllowedValues('currency', function ($value) {
                return in_array($value, CurrencyEnum::CURRENCIES);
            })
            ->setAllowedTypes('userId', ['string'])
            ->setAllowedTypes('invoiceId', ['string'])
            ->setAllowedTypes('paymentDestination', ['array'])
            ->setAllowedValues('paymentDestination', function (&$data) {
                $resolver = new OptionsResolver();
                $this->configurePaymentDestinationOptionsResolver($resolver);
                $data = $resolver->resolve($data);

                return true;
            })
            ->setAllowedTypes('externalId', ['null', 'string'])
            ->setAllowedTypes('quoteId', ['null', 'string'])
            ->setAllowedTypes('payoutLabel', ['null', 'string'])
            ->setAllowedTypes('attachments', ['array'])
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
            ->setAllowedTypes('guarantor', ['null', 'string'])
            ->setAllowedValues('guarantor', function ($value) {
                return is_null($value) || in_array($value, [self::GUARANTOR_COMPANY]);
            })
            ->setAllowedTypes('preferInstantPayment', ['null', 'bool'])
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

    protected function configurePaymentDestinationOptionsResolver(OptionsResolver $resolver): void
    {
        $resolver->setDefined([
            'accountNumberType',
            'accountNumber',
            'routingNumberType',
            'routingNumber',
            'type',
        ]);

        $resolver->setRequired([
            'accountNumberType',
            'accountNumber',
            'type',
        ]);

        $resolver->setDefaults([
            'accountNumberType' => self::PAYMENT_DESTINATION_ACCOUNT_NUMBER_TYPE_IBAN,
            'type' => self::PAYMENT_DESTINATION_TYPE_BANK_ACCOUNT,
        ]);

        $resolver
            ->setAllowedTypes('accountNumberType', ['string'])
            ->setAllowedValues('accountNumberType', function ($value) {
                return in_array($value, [self::PAYMENT_DESTINATION_ACCOUNT_NUMBER_TYPE_IBAN]);
            })
            ->setAllowedTypes('accountNumber', ['string'])
            ->setAllowedTypes('routingNumberType', ['null', 'string'])
            ->setAllowedTypes('routingNumber', ['null', 'string'])
            ->setAllowedTypes('type', ['string'])
            ->setAllowedValues('type', function ($value) {
                return in_array($value, [self::PAYMENT_DESTINATION_TYPE_BANK_ACCOUNT]);
            })
        ;
    }
}
