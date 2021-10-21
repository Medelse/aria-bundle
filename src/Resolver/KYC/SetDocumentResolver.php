<?php

namespace Medelse\AriaBundle\Resolver\KYC;

use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class SetDocumentResolver
 * Resolve options for Set a KYV Document endpoint
 */
class SetDocumentResolver
{
    private const ALLOWED_MIME_TYPES = [
        'image/jpeg',
        'image/png',
        'image/tiff',
        'application/pdf',
    ];

    public function resolve(array $data): array
    {
        $resolver = new OptionsResolver();
        $this->configureOptionsResolver($resolver);
        $data = $resolver->resolve($data);

        $document = new DataPart($data['document'], $data['fileName'], 'multipart/form-data');
        return [
            'document' => $document,
        ];
    }

    private function configureOptionsResolver(OptionsResolver $resolver): void
    {
        $resolver->setDefined([
            'document',
            'fileName',
            'mimeType',
        ]);

        $resolver->setRequired([
            'document',
            'fileName',
            'mimeType',
        ]);

        $resolver
            ->setAllowedTypes('document', ['string'])
            ->setAllowedTypes('fileName', ['string'])
            ->setAllowedTypes('mimeType', ['string'])
            ->setAllowedValues('mimeType', function ($value) {
                return in_array($value, self::ALLOWED_MIME_TYPES);
            })
        ;
    }
}
