<?php

namespace Medelse\AriaBundle\Enum;


class BusinessTypeEnum
{
    public const COMPANY = 'COMPANY';
    public const INDIVIDUAL = 'INDIVIDUAL';

    public static function getAllowedValues(): array
    {
        return [
            self::COMPANY,
            self::INDIVIDUAL,
        ];
    }
}
