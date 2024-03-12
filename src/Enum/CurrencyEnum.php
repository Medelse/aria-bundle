<?php

namespace Medelse\AriaBundle\Enum;


class CurrencyEnum
{
    public const CURRENCY_EUR = 'EUR';
    public const CURRENCY_GBP = 'GBP';
    public const CURRENCY_USD = 'USD';
    public const CURRENCIES = [
        self::CURRENCY_EUR,
        self::CURRENCY_GBP,
        self::CURRENCY_USD,
    ];

}
