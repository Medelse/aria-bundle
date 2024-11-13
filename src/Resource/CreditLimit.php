<?php

namespace Medelse\AriaBundle\Resource;

class CreditLimit extends Resource
{
    public const GET_FROM_SIREN = '/credit-limits';

    // We use a like filter because companies can be registered from their SIREN or SIRET number
    private const GET_FROM_SIREN_FILTER = 'like(debtorIdentifier.value,{siren}%)';


    public function getFromSiren(string $siren): array
    {
        $filter = str_replace('{siren}', $siren, self::GET_FROM_SIREN_FILTER);

        return $this->sendGetRequest(self::GET_FROM_SIREN, [
            'filter' => $filter,
        ]);
    }
}
