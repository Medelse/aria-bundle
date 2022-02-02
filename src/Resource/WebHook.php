<?php

namespace Medelse\AriaBundle\Resource;

use Medelse\AriaBundle\Resolver\WebHook\CreateWebHookResolver;
use Symfony\Component\HttpFoundation\Request;

class WebHook extends Resource
{
    public const EVENT_ADVANCE_CREATED = 'advance.created';
    public const EVENT_ADVANCE_UPDATED = 'advance.updated';
    public const EVENT_KYC_UPDATED = 'kyc.updated';
    public const EVENT_CONTRACT_CREATED = 'contract.created';
    public const EVENT_CONTRACT_UPDATED = 'contract.updated';

    public const CREATE_WEBHOOK_URL = '/subscriptions/webhook';

    /**
     * @param array $data
     * @return array The webhook created
     */
    public function createNewWebHookSubscription(array $data): array
    {
        $createResolver = new CreateWebHookResolver();
        $data = $createResolver->resolve($data);
        $path = self::CREATE_WEBHOOK_URL;

        return $this->sendPostOrPatchRequest(Request::METHOD_POST, $path, $data);
    }
}
