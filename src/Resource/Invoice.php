<?php

namespace Medelse\AriaBundle\Resource;

use Medelse\AriaBundle\Resolver\Invoice\CreateInvoiceResolver;
use Medelse\AriaBundle\Resolver\Invoice\UpdateInvoiceResolver;
use Symfony\Component\HttpFoundation\Request;

class Invoice extends Resource
{
    public const CREATE_URL = '/invoices';
    public const UPDATE_URL = '/invoices/{invoiceId}';
    public const GET_URL = '/invoices/{invoiceId}';
    public const DELETE_URL = '/invoices/{invoiceId}';

    public function createInvoice(array $data): array
    {
        $resolver = new CreateInvoiceResolver();
        $data = $resolver->resolve($data);
        $path = self::CREATE_URL;

        return $this->sendPostOrPatchRequest(Request::METHOD_POST, $path, $data);
    }

    public function updateInvoice(string $invoiceId, array $data): array
    {
        $updateResolver = new UpdateInvoiceResolver();
        $data = $updateResolver->resolve($data);
        $path = str_replace(
            '{invoiceId}',
            $invoiceId,
            self::UPDATE_URL
        );

        return $this->sendPostOrPatchRequest(Request::METHOD_PATCH, $path, $data);
    }

    public function getInvoice(string $invoiceId): array
    {
        $path = str_replace(
            '{invoiceId}',
            $invoiceId,
            self::GET_URL
        );
        return $this->sendGetRequest($path);
    }

    public function deleteInvoice(string $invoiceId): array
    {
        $path = str_replace(
            '{invoiceId}',
            $invoiceId,
            self::DELETE_URL
        );
        return $this->sendDeleteRequest($path);
    }
}
