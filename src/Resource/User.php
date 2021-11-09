<?php

namespace Medelse\AriaBundle\Resource;

use Medelse\AriaBundle\Resolver\User\CreateUserResolver;

class User extends Resource
{
    public const KYC_UNNEEDED_KEY = 'UNNEEDED';
    public const KYC_EMPTY_KEY = 'EMPTY';
    public const KYC_PENDING_KEY = 'PENDING';
    public const KYC_VALID_KEY = 'VALID';
    public const KYC_INVALID_KEY = 'INVALID';

    public const CONTRACT_STATUS_NOT_SEND_KEY = 'notSent';
    public const CONTRACT_STATUS_PENDING_KEY = 'pending';
    public const CONTRACT_STATUS_SIGNED_KEY = 'signed';
    public const CONTRACT_STATUS_COMPLETED_KEY = 'completed';

    public const CREATE_USER_URL = '/user';
    public const GET_USER_URL = '/user/{userId}';
    public const SEND_USER_CONTRACT_URL = '/user/{userId}/contract';

    /**
     * @param array $data
     * @return array The user created
     */
    public function createUser(array $data): array
    {
        $createResolver = new CreateUserResolver();
        $data = $createResolver->resolve($data);
        $path = self::CREATE_USER_URL;

        return $this->sendPostRequest($path, $data);
    }

    /**
     * @param string $userId
     * @return array
     */
    public function getUser(string $userId): array
    {
        $path = str_replace('{userId}', $userId, self::GET_USER_URL);
        
        return $this->sendGetRequest($path);
    }

    /**
     * @param string $userId
     * @return array
     */
    public function sendUserContract(string $userId): array
    {
        $path = str_replace(
            '{userId}',
            $userId,
            self::SEND_USER_CONTRACT_URL
        );

        return $this->sendPostRequest($path);
    }
}
