<?php

namespace Medelse\AriaBundle\Resource;

use Medelse\AriaBundle\Security\BearerGenerator;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mime\Part\Multipart\FormDataPart;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

abstract class Resource
{
    public const AUTH_METADATA_KEY = 'authorization';

    protected HttpClientInterface $httpClient;
    protected BearerGenerator $bearerGenerator;
    protected string $ariaBaseUrl;

    public function __construct(HttpClientInterface $httpClient, BearerGenerator $bearerGenerator, string $ariaBaseUrl)
    {
        $this->httpClient = $httpClient;
        $this->bearerGenerator = $bearerGenerator;
        $this->ariaBaseUrl = $ariaBaseUrl;
    }

    protected function sendGetRequest(string $path): array
    {
        $response = $this->httpClient->request(
            Request::METHOD_GET,
            $this->ariaBaseUrl . $path,
            [
                'headers' => [
                    self::AUTH_METADATA_KEY => 'Bearer ' . $this->bearerGenerator->getBearerToken(),
                ],
            ]
        );

        return $this->processResponse($response);
    }

    protected function sendPostOrPatchRequest(string $method, string $path, array $body = []): array
    {
        $allowedMethods = [Request::METHOD_POST, Request::METHOD_PATCH];
        if (!in_array($method, $allowedMethods)) {
            throw new \InvalidArgumentException(sprintf('Allowed http methods for function sendPostOrPatchRequest are %s', implode(', ', $allowedMethods)));
        }

        $response = $this->httpClient->request(
            $method,
            $this->ariaBaseUrl . $path,
            [
                'json' => $body,
                'headers' => [
                    self::AUTH_METADATA_KEY => 'Bearer ' . $this->bearerGenerator->getBearerToken(),
                ],
            ]
        );

        return $this->processResponse($response);
    }

    protected function sendRequestFormData(string $method, string $path, array $body): array
    {
        $allowedMethods = [Request::METHOD_POST, Request::METHOD_PUT];
        if (!in_array($method, $allowedMethods)) {
            throw new \InvalidArgumentException(sprintf('Allowed http methods for function sendRequestFormData are %s', implode(', ', $allowedMethods)));
        }

        $formData = new FormDataPart($body);

        $response = $this->httpClient->request(
            $method,
            $this->ariaBaseUrl . $path,
            [
                'headers' => array_merge(
                    $formData->getPreparedHeaders()->toArray(),
                    [
                        self::AUTH_METADATA_KEY => 'Bearer ' . $this->bearerGenerator->getBearerToken(),
                    ]
                ),
                'body' => $formData->bodyToIterable(),
            ]
        );

        return $this->processResponse($response);
    }

    protected function processResponse(ResponseInterface $response): array
    {
        $data = $response->toArray(false);

        if (strpos((string)$response->getStatusCode(), '2') !== 0) {
            $status  = isset($data['status']) ? $data['status'] : $response->getStatusCode();
            $code    = isset($data['code']) ? $data['code'] : '';
            $message = isset($data['message']) ? $data['message'] : 'An unexpected error occured';

            throw new BadRequestException(sprintf('Error %s %s: %s', $status, $code, $message));
        }

        if (isset($data['status']) && isset($data['message']) && isset($data['code'])) {
            throw new BadRequestException(sprintf('Error %s %s: %s', $data['status'], $data['code'], $data['message']));
        }

        return $data;
    }
}
