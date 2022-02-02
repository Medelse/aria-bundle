<?php

namespace Medelse\AriaBundle\Resolver\WebHook;

use Medelse\AriaBundle\Resource\WebHook;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateWebHookResolver
{
    public function resolve(array $data): array
    {
        $resolver = new OptionsResolver();
        $this->configureOptionsResolver($resolver);
        return $resolver->resolve($data);
    }

    private function configureOptionsResolver(OptionsResolver $resolver): void
    {
        $resolver->setDefined([
            'options',
            'event',
        ]);

        $resolver->setRequired([
            'options',
            'event',
        ]);

        $resolver
            ->setAllowedTypes('options', ['array'])
            ->setAllowedValues('options', function ($value) {
                if (empty($value['endpoint'])) {
                    throw new InvalidOptionsException('Option "options" must have an "endpoint" key and its value must not be empty');
                }

                if (!empty($value['secret']) and (strlen($value['secret']) < 16 || strlen($value['secret']) > 128)) {
                    throw new InvalidOptionsException('Key "secret" of option "options" must be null or its length must be between 16 and 128 characters');
                }

                return is_array($value) && !empty($value['endpoint']);
            })
            ->setAllowedTypes('event', ['string'])
            ->setAllowedValues('event', function ($value) {
                return in_array(
                    $value,
                    [
                        WebHook::EVENT_ADVANCE_CREATED,
                        WebHook::EVENT_ADVANCE_UPDATED,
                        WebHook::EVENT_KYC_UPDATED,
                        WebHook::EVENT_CONTRACT_CREATED,
                        WebHook::EVENT_CONTRACT_UPDATED
                    ]
                );
            })
        ;
    }
}
