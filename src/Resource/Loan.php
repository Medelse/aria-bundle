<?php

namespace Medelse\AriaBundle\Resource;

use Medelse\AriaBundle\Resolver\Loan\CreateLoanResolver;
use Medelse\AriaBundle\Resolver\Loan\UpdateLoanResolver;
use Symfony\Component\HttpFoundation\Request;

class Loan extends Resource
{
    public const CREATE_URL = '/loans';
    public const UPDATE_URL = '/loans/{loanId}';
    public const GET_URL = '/loans/{loanId}';
    public const CANCEL_URL = '/loans/{loanId}/cancel';
    public const RETRY_PAYMENT_URL = '/loans/{loanId}/retry-payment';
    public const DELETE_URL = '/loans/{loanId}';

    public function createLoan(array $data): array
    {
        $resolver = new CreateLoanResolver();
        $data = $resolver->resolve($data);
        $path = self::CREATE_URL;

        return $this->sendPostOrPatchRequest(Request::METHOD_POST, $path, $data);
    }

    public function updateLoan(string $loanId, array $data): array
    {
        $updateResolver = new UpdateLoanResolver();
        $data = $updateResolver->resolve($data);
        $path = str_replace(
            '{loanId}',
            $loanId,
            self::UPDATE_URL
        );

        return $this->sendPostOrPatchRequest(Request::METHOD_PATCH, $path, $data);
    }

    public function getLoan(string $loanId): array
    {
        $path = str_replace(
            '{loanId}',
            $loanId,
            self::GET_URL
        );
        return $this->sendGetRequest($path);
    }

    public function retryPaymentLoan(string $loanId): array
    {
        $path = str_replace(
            '{loanId}',
            $loanId,
            self::RETRY_PAYMENT_URL
        );
        return $this->sendPostOrPatchRequest(Request::METHOD_POST, $path);
    }

    public function cancelLoan(string $loanId): array
    {
        $path = str_replace(
            '{loanId}',
            $loanId,
            self::CANCEL_URL
        );
        return $this->sendPostOrPatchRequest(Request::METHOD_POST, $path);
    }

    public function deleteLoan(string $loanId): array
    {
        $path = str_replace(
            '{loanId}',
            $loanId,
            self::DELETE_URL
        );
        return $this->sendDeleteRequest($path);
    }
}
