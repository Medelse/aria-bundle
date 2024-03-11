<?php

namespace Medelse\AriaBundle\Resolver\Loan;

use Medelse\AriaBundle\Enum\CurrencyEnum;
use Medelse\AriaBundle\Resolver\Loan\CreateLoanResolver;
use Medelse\AriaBundle\Tool\ArrayFormatter;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UpdateLoanResolver extends CreateLoanResolver
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

        $resolver
            ->setAllowedTypes('amount', ['null', 'numeric'])
            ->setAllowedValues('amount', function ($value) {
                return is_null($value) || $value > 0;
            })
            ->setAllowedTypes('currency', ['null', 'string'])
            ->setAllowedValues('currency', function ($value) {
                return is_null($value) || in_array($value, CurrencyEnum::CURRENCIES);
            })
            ->setAllowedTypes('userId', ['null', 'string'])
            ->setAllowedTypes('invoiceId', ['null', 'string'])
            ->setAllowedTypes('paymentDestination', ['null', 'array'])
            ->setAllowedValues('paymentDestination', function (&$data) {
                if (!empty($data)) {
                    $resolver = new OptionsResolver();
                    $this->configurePaymentDestinationOptionsResolver($resolver);
                    $data = $resolver->resolve($data);
                }

                return true;
            })
            ->setAllowedTypes('externalId', ['null', 'string'])
            ->setAllowedTypes('quoteId', ['null', 'string'])
            ->setAllowedTypes('payoutLabel', ['null', 'string'])
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
            ->setAllowedTypes('guarantor', ['null', 'string'])
            ->setAllowedValues('guarantor', function ($value) {
                return is_null($value) || in_array($value, [self::GUARANTOR_COMPANY]);
            })
            ->setAllowedTypes('preferInstantPayment', ['null', 'bool'])
        ;
    }
}
