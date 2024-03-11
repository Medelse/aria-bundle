<?php

namespace Medelse\AriaBundle\Enum;

class LoanStatusEnum
{
    public const STATUS_CREATED = 'CREATED';
    public const STATUS_BLOCKED = 'BLOCKED';
    public const STATUS_ACCEPTED = 'ACCEPTED';
    public const STATUS_REFUSED = 'REFUSED';
    public const STATUS_CANCELED = 'CANCELED';
    public const STATUS_PAYMENT_ORDERED = 'PAYMENT_ORDERED';
    public const STATUS_PAYMENT_FAILED = 'PAYMENT_FAILED';
    public const STATUS_PAYMENT_RETRY_REQUESTED = 'PAYMENT_RETRY_REQUESTED';
    public const STATUS_PAYMENT_CANCELED = 'PAYMENT_CANCELED';
    public const STATUS_TO_REPAY = 'TO_REPAY';
    public const STATUS_PAST_DUE = 'PAST_DUE';
    public const STATUS_REPAID = 'REPAID';
}
