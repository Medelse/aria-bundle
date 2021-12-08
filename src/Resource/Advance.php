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
}
