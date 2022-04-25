<?php

namespace Medelse\AriaBundle\Resource;

use Medelse\AriaBundle\Resolver\Advance\CreateAdvanceResolver;
use Symfony\Component\HttpFoundation\Request;

class Advance extends Resource
{
    public const STATUS_PROCESSING = 'PROCESSING'; // We didn't check the advance yet.
    public const STATUS_ACCEPTED   = 'ACCEPTED'; // We accepted your advance but did not make the payment yet.
    public const STATUS_SENT       = 'SENT'; // The order has been transferred to our banking provider, the transaction is not yet on the SEPA network.
    public const STATUS_PENDING    = 'PENDING'; // We accepted the advance, made the payment, and are waiting for the repayment.
    public const STATUS_REFUSED    = 'REFUSED'; // We refused the advance because of the information filled or the end client solvability.
    public const STATUS_PAID       = 'PAID'; // We received your payment.
    public const STATUS_LATE       = 'LATE'; // Your payment is late.
    public const STATUS_FAILED     = 'FAILED'; // We made the payment but it failed. Please contact us.

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
