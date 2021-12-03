<?php

namespace Medelse\AriaBundle\Resource;

use Medelse\AriaBundle\Resolver\KYC\SetDocumentResolver;
use Symfony\Component\HttpFoundation\Request;

class Document extends Resource
{
    public const ID_KEY = 'ID';

    public const SET_DOCUMENT_URL = '/user/{userId}/kyc/{type}';

    /**
     * @param array $data
     * @return array The document added information
     */
    public function sendDocumentId(array $data, string $ariaId): array
    {
        $createResolver = new SetDocumentResolver();
        $data = $createResolver->resolve($data);
        $path = str_replace(
            ['{userId}', '{type}'],
            [$ariaId, self::ID_KEY],
            self::SET_DOCUMENT_URL
        );

        return $this->sendRequestFormData(Request::METHOD_PUT, $path, $data);
    }
}
