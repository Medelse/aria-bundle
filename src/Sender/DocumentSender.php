<?php

namespace Medelse\AriaBundle\Sender;

use Medelse\AriaBundle\Resolver\KYC\SetDocumentResolver;

class DocumentSender extends Sender
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

        return $this->sendPutRequestFormData($path, $data);
    }
}
