<?php

namespace Medelse\AriaBundle\Resolver\Advance;

use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateAdvanceResolver
{
    public const CURRENCY_EUR = 'EUR';

    public function resolve(array $data): array
    {
        $resolver = new OptionsResolver();
        $this->configureOptionsResolver($resolver);
        $data = $resolver->resolve($data);

        $ariaData = [
            'billDate'     => $data['billDate'],
            'amount'       => $data['amount'],
            'currency'     => $data['currency'],
            'duration'     => $data['duration'],
            'customerName' => $data['customerName'],
            'customerSiren'=> $data['customerSiren'],
            'invoiceNumber'=> isset($data['invoiceNumber']) ? $data['invoiceNumber'] : '',
            'label'        => isset($data['label']) ? $data['label'] : '',
        ];

        if (isset($data['externalId'])) {
            $ariaData['externalId'] = $data['externalId'];
        }

        foreach ($data['bill'] as $bill) {
            $ariaData[] = ['bill' => new DataPart($bill['document'], $bill['fileName'], 'multipart/form-data')];
        }

        return $ariaData;
    }

    private function configureOptionsResolver(OptionsResolver $resolver): void
    {
        $resolver->setDefined([
            'bill',
            'billDate',
            'amount',
            'currency',
            'duration',
            'customerName',
            'customerSiren',
            'invoiceNumber',
            'label',
            'externalId',
        ]);

        $resolver->setRequired([
            'bill',
            'billDate',
            'amount',
            'currency',
            'duration',
            'customerName',
            'customerSiren',
        ]);

        $resolver->setDefaults([
            'currency' => self::CURRENCY_EUR,
        ]);

        $resolver
            ->setAllowedTypes('bill', ['array'])
            ->setAllowedValues('bill', function ($value) {
                if (empty($value)) {
                    throw new InvalidOptionsException('Option "bill" cannot be empty');
                }

                foreach ($value as $bill) {
                    if (!is_array($bill) || empty($bill['document']) || empty($bill['fileName'])) {
                        throw new InvalidOptionsException('Option "bill" must be an array and have document and fileName keys');
                    }
                }

                return true;
            })
            ->setAllowedTypes('billDate', [\DateTimeInterface::class])
            ->setNormalizer('billDate', function (Options $options, $value) {
                return $value->format(\DateTimeInterface::ISO8601);
            })
            ->setAllowedTypes('amount', ['numeric'])
            ->setAllowedValues('amount', function ($value) {
                return $value > 0;
            })
            ->setNormalizer('amount', function (Options $options, $value) {
                return (string) $value;
            })
            ->setAllowedTypes('currency', ['string'])
            ->setAllowedTypes('duration', ['numeric'])
            ->setNormalizer('duration', function (Options $options, $value) {
                return (string) $value;
            })
            ->setAllowedTypes('customerName', ['string'])
            ->setAllowedTypes('customerSiren', ['string'])
            ->setNormalizer('customerSiren', function (Options $options, $value) {
                return str_replace(' ', '', $value);
            })
            ->setAllowedValues('customerSiren', function ($value) {
                return preg_match('/^[0-9]{9}$/', str_replace(' ', '', $value));
            })
            ->setAllowedTypes('invoiceNumber', ['null', 'string', 'numeric'])
            ->setNormalizer('invoiceNumber', function (Options $options, $value) {
                return (string) $value;
            })
            ->setAllowedTypes('label', ['null', 'string'])
            ->setAllowedTypes('externalId', ['null', 'string'])
        ;
    }
}
