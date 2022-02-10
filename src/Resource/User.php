<?php

namespace Medelse\AriaBundle\Resource;

use Medelse\AriaBundle\Resolver\User\CreateUserResolver;
use Medelse\AriaBundle\Resolver\User\UpdateUserResolver;
use Symfony\Component\HttpFoundation\Request;

class User extends Resource
{
    public const KYC_UNNEEDED_KEY = 'UNNEEDED';
    public const KYC_EMPTY_KEY = 'EMPTY';
    public const KYC_PENDING_KEY = 'PENDING';
    public const KYC_VALID_KEY = 'VALID';
    public const KYC_INVALID_KEY = 'INVALID';

    public const USER_CONTRACT_STATUS_NOT_SEND_KEY = 'notSent';
    public const USER_CONTRACT_STATUS_PENDING_KEY = 'pending';
    public const USER_CONTRACT_STATUS_SIGNED_KEY = 'signed';
    public const USER_CONTRACT_STATUS_COMPLETED_KEY = 'completed';
    public const CONTRACT_STATUS_NOT_SEND_KEY = 'NOTSENT';
    public const CONTRACT_STATUS_PENDING_KEY = 'PENDING';
    public const CONTRACT_STATUS_SIGNED_KEY = 'SIGNED';
    public const CONTRACT_STATUS_COMPLETED_KEY = 'COMPLETED';

    public const CREATE_USER_URL = '/user';
    public const UPDATE_USER_URL = '/user/{userId}';
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

        return $this->sendPostOrPatchRequest(Request::METHOD_POST, $path, $data);
    }

    /**
     * @param string $userId
     * @param array  $data
     * @return array The user updated
     */
    public function updateUser(string $userId, array $data): array
    {
        $updateResolver = new UpdateUserResolver();
        $data = $updateResolver->resolve($data);
        $path = str_replace(
            '{userId}',
            $userId,
            self::UPDATE_USER_URL
        );

        return $this->sendPostOrPatchRequest(Request::METHOD_PATCH, $path, $data);
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

        return $this->sendPostOrPatchRequest(Request::METHOD_POST, $path);
    }
}
