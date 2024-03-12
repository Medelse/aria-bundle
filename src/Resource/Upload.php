<?php

namespace Medelse\AriaBundle\Resource;

use Medelse\AriaBundle\Resolver\Upload\UploadFileResolver;
use Symfony\Component\HttpFoundation\Request;

class Upload extends Resource
{
    public const CREATE_URL = '/upload';

    public function uploadFile(array $data): array
    {
        $resolver = new UploadFileResolver();
        $data = $resolver->resolve($data);
        $path = self::CREATE_URL;

        return $this->sendRequestFormData(Request::METHOD_POST, $path, $data);
    }
}
