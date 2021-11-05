<?php

namespace Medelse\AriaBundle\Sender;

use Medelse\AriaBundle\Resolver\User\CreateUserResolver;

class UserSender extends Sender
{
    public const CREATE_USER_URL = '/user';
    public const GET_USER_URL = '/user/{userId}';

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
}
