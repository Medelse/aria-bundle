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
    public const EVENT_LOAN_CREATED = 'loan.created';
    public const EVENT_LOAN_UPDATED = 'loan.updated';
    public const EVENT_LOAN_DELETED = 'loan.deleted';
    public const EVENT_CREDIT_LIMIT_CREATED = 'credit-limit.created';
    public const EVENT_CREDIT_LIMIT_UPDATED = 'credit-limit.updated';
    public const EVENT_CREDIT_LIMIT_DELETED = 'credit-limit.deleted';

    public const CREATE_WEBHOOK_URL = '/subscriptions/webhook';
    public const DELETE_WEBHOOK_URL = '/subscriptions/{subscriptionId}';

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

    public function deleteSubscription(string $subscriptionId): array
    {
        $path = str_replace(
            '{subscriptionId}',
            $subscriptionId,
            self::DELETE_WEBHOOK_URL
        );
        return $this->sendDeleteRequest($path);
    }
}
