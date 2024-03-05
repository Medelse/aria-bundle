<?php

namespace Medelse\AriaBundle\Resolver\Upload;

use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UploadFileResolver
{
    private const ALLOWED_MIME_TYPES = [
        'image/jpeg',
        'image/png',
        'application/pdf',
    ];

    public function resolve(array $data): array
    {
        $resolver = new OptionsResolver();
        $this->configureOptionsResolver($resolver);
        $data = $resolver->resolve($data);

        return [
            'file' => new DataPart(
                $data['document'],
                $data['fileName'],
                $data['contentType']
            ),
        ];
    }

    private function configureOptionsResolver(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefined([
                'document',
                'fileName',
                'contentType',
            ])
            ->setRequired([
                'document',
                'contentType',
            ])
            ->setDefaults([
                'fileName' => 'file',
            ])
            ->setAllowedTypes('document', ['string'])
            ->setAllowedTypes('contentType', ['string'])
            ->setAllowedValues('contentType', function ($value) {
                return in_array(
                    $value,
                    self::ALLOWED_MIME_TYPES
                );
            })
        ;
    }
}
