<?php

namespace Medelse\AriaBundle\Resolver\User;

use Medelse\AriaBundle\Tool\ArrayFormatter;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateUserResolver
{
    public function resolve(array $data): array
    {
        $resolver = new OptionsResolver();
        $this->configureOptionsResolver($resolver);
        $data = $resolver->resolve($data);

        $bankAccount = [];
        if ($data['bankAccountIBAN']) {
            $bankAccount['IBAN'] = $data['bankAccountIBAN'];
        }
        if ($data['bankAccountBIC']) {
            $bankAccount['BIC'] = $data['bankAccountBIC'];
        }

        $dataToReturn = [
            'phone' => $data['phone'],
            'email' => $data['email'],
            'givenName' => $data['givenName'],
            'familyName' => $data['familyName'],
            'address' => [
                'first' => $data['addressFirst'],
                'second' => $data['addressSecond'],
                'city' => $data['addressCity'],
                'region' => $data['addressRegion'],
                'postal' => $data['addressPostal'],
                'country' => $data['addressCountry'],
            ],
            'siren' => $data['siren'],
            'businessName' => $data['businessName'],
            'bankAccount' => $bankAccount,
        ];

        return ArrayFormatter::removeNullValues($dataToReturn);
    }

    private function configureOptionsResolver(OptionsResolver $resolver): void
    {
        $resolver->setDefined([
            'phone',
            'email',
            'givenName',
            'familyName',
            'addressFirst',
            'addressSecond',
            'addressCity',
            'addressRegion',
            'addressPostal',
            'addressCountry',
            'siren',
            'businessName',
            'bankAccountIBAN',
            'bankAccountBIC',
        ]);

        $resolver->setRequired([
            'email',
            'addressFirst',
            'addressCity',
            'addressPostal',
            'addressCountry',
            'bankAccountIBAN',
        ]);

        $resolver
            ->setAllowedTypes('phone', ['null', 'string'])
            ->setAllowedTypes('email', ['string'])
            ->setAllowedValues('email', function ($value) {
                return filter_var($value, FILTER_VALIDATE_EMAIL);
            })
            ->setAllowedTypes('givenName', ['null', 'string'])
            ->setAllowedTypes('familyName', ['null', 'string'])
            ->setAllowedTypes('addressFirst', ['string'])
            ->setAllowedTypes('addressSecond', ['null', 'string'])
            ->setAllowedTypes('addressCity', ['string'])
            ->setAllowedTypes('addressRegion', ['null', 'string'])
            ->setAllowedTypes('addressPostal', ['string'])
            ->setAllowedTypes('addressCountry', ['string']) // Country code (ISO-3166-Alpha2)
            ->setNormalizer('addressCountry', function (Options $options, $value) {
                return strtoupper($value);
            })
            ->setAllowedValues('addressCountry', function ($value) {
                return preg_match('/^[a-zA-Z]{2}$/', $value);
            })
            ->setAllowedTypes('siren', ['null', 'string', 'numeric'])
            ->setNormalizer('siren', function (Options $options, $value) {
                return is_string($value) ? str_replace(' ', '', $value) : $value;
            })
            ->setAllowedValues('siren', function ($value) {
                return preg_match('/^[0-9]{9}$/', str_replace(' ', '', $value));
            })
            ->setAllowedTypes('businessName', ['null', 'string'])
            ->setAllowedTypes('bankAccountIBAN', ['string'])
            ->setNormalizer('bankAccountIBAN', function (Options $options, $value) {
                return strtoupper(str_replace(' ', '', $value));
            })
            ->setAllowedTypes('bankAccountBIC', ['null', 'string'])
        ;
    }
}
