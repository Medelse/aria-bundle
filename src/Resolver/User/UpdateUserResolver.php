<?php

namespace Medelse\AriaBundle\Resolver\User;

use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UpdateUserResolver
{
    public function resolve(array $data): array
    {
        $resolver = new OptionsResolver();
        $this->configureOptionsResolver($resolver);
        $data = $resolver->resolve($data);

        $ariaData = [];

        if (!empty($data['phone'])) {
            $ariaData['phone'] = $data['phone'];
        }

        if (!empty($data['email'])) {
            $ariaData['email'] = $data['email'];
        }

        if (!empty($data['givenName'])) {
            $ariaData['givenName'] = $data['givenName'];
        }

        if (!empty($data['familyName'])) {
            $ariaData['familyName'] = $data['familyName'];
        }

        if (!empty($data['siren'])) {
            $ariaData['siren'] = $data['siren'];
        }

        if (!empty($data['businessName'])) {
            $ariaData['businessName'] = $data['businessName'];
        }

        if (!empty($data['bankAccountIBAN'])) {
            $ariaData['IBAN'] = $data['bankAccountIBAN'];
        }

        if (!empty($data['addressFirst'])
            || !empty($data['addressSecond'])
            || !empty($data['addressCity'])
            || !empty($data['addressRegion'])
            || !empty($data['addressPostal'])
            || !empty($data['addressCountry'])
        ) {
            $ariaData['address'] = [
                'first' => $data['addressFirst'],
                'second' => $data['addressSecond'],
                'city' => $data['addressCity'],
                'region' => $data['addressRegion'],
                'postal' => $data['addressPostal'],
                'country' => $data['addressCountry'],
            ];
        }

        return $ariaData;
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
        ]);

        $resolver->setDefaults([
            'addressFirst' => null,
            'addressSecond' => null,
            'addressCity' => null,
            'addressRegion' => null,
            'addressPostal' => null,
            'addressCountry' => null,
        ]);

        $resolver
            ->setAllowedTypes('phone', ['null', 'string'])
            ->setAllowedTypes('email', ['null', 'string'])
            ->setAllowedValues('email', function ($value) {
                return filter_var($value, FILTER_VALIDATE_EMAIL);
            })
            ->setAllowedTypes('givenName', ['null', 'string'])
            ->setAllowedTypes('familyName', ['null', 'string'])
            ->setAllowedTypes('addressFirst', ['null', 'string'])
            ->setNormalizer('addressFirst', function (Options $options, $value) {
                if (!empty($value)) {
                    if (empty($options['addressCity']) || empty($options['addressPostal']) || empty($options['addressCountry'])) {
                        throw new MissingOptionsException('Options addressCity, addressPostal, addressCountry are required to update the addressFirst');
                    }
                }

                if (!empty($options['addressCity'])) {
                    if (empty($value) || empty($options['addressPostal']) || empty($options['addressCountry'])) {
                        throw new MissingOptionsException('Options addressFirst, addressPostal, addressCountry are required to update the addressCity');
                    }
                }

                if (!empty($options['addressPostal'])) {
                    if (empty($value) || empty($options['addressCity']) || empty($options['addressCountry'])) {
                        throw new MissingOptionsException('Options addressFirst, addressCity, addressCountry are required to update the addressPostal');
                    }
                }

                if (!empty($options['addressCountry'])) {
                    if (empty($value) || empty($options['addressCity']) || empty($options['addressPostal'])) {
                        throw new MissingOptionsException('Options addressFirst, addressCity, addressPostal are required to update the addressCountry');
                    }
                }

                if (!empty($options['addressSecond']) || !empty($options['addressRegion'])) {
                    if (empty($value) || empty($options['addressCity']) || empty($options['addressPostal']) || empty($options['addressCountry'])) {
                        throw new MissingOptionsException('Options addressFirst, addressCity, addressPostal, addressCountry are required to update the address');
                    }
                }

                return $value;
            })
            ->setAllowedTypes('addressSecond', ['null', 'string'])
            ->setAllowedTypes('addressCity', ['null', 'string'])
            ->setAllowedTypes('addressRegion', ['null', 'string'])
            ->setAllowedTypes('addressPostal', ['null', 'string'])
            ->setAllowedTypes('addressCountry', ['null', 'string']) // Country code (ISO-3166-Alpha2)
            ->setNormalizer('addressCountry', function (Options $options, $value) {
                return strtoupper($value);
            })
            ->setAllowedValues('addressCountry', function ($value) {
                return null === $value || preg_match('/^[a-zA-Z]{2}$/', $value);
            })
            ->setAllowedTypes('siren', ['null', 'string', 'numeric'])
            ->setNormalizer('siren', function (Options $options, $value) {
                return is_string($value) ? str_replace(' ', '', $value) : $value;
            })
            ->setAllowedValues('siren', function ($value) {
                return preg_match('/^[0-9]{9}$/', str_replace(' ', '', $value));
            })
            ->setAllowedTypes('businessName', ['null', 'string'])
            ->setAllowedTypes('bankAccountIBAN', ['null', 'string'])
            ->setNormalizer('bankAccountIBAN', function (Options $options, $value) {
                return strtoupper(str_replace(' ', '', $value));
            })
        ;
    }
}
