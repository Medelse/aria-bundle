<?php

namespace Medelse\AriaBundle\Resource;

use Medelse\AriaBundle\Resolver\Advance\CreateAdvanceResolver;
use Symfony\Component\HttpFoundation\Request;

class Advance extends Resource
{
    public const STATUS_PROCESSING = 'PROCESSING';
    public const STATUS_REFUSED    = 'REFUSED';
    public const STATUS_SENT       = 'SENT';
    public const STATUS_FAILED     = 'FAILED';
    public const STATUS_PENDING    = 'PENDING';
    public const STATUS_PAID       = 'PAID';
    public const STATUS_LATE       = 'LATE';

    public const CREATE_ADVANCE_URL = '/user/{userId}/advance';
    public const GET_ADVANCE_URL    = '/user/{userId}/advance/{advanceId}';

    /**
     * @return array The created advance
     */
    public function createAdvance(array $data, string $ariaId): array
    {
        $resolver = new CreateAdvanceResolver();
        $data = $resolver->resolve($data);
        $path = str_replace(
            ['{userId}'],
            [$ariaId],
            self::CREATE_ADVANCE_URL
        );

        return $this->sendRequestFormData(Request::METHOD_POST, $path, $data);
    }

    /**
     * @return array The requested advance
     */
    public function getAdvance(string $ariaId, string $advanceId): array
    {
        $path = str_replace(
            ['{userId}', '{advanceId}'],
            [$ariaId, $advanceId],
            self::GET_ADVANCE_URL
        );
        return $this->sendGetRequest($path);
    }
}
