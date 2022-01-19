<?php

namespace Medelse\AriaBundle\Resource;

use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mime\Part\Multipart\FormDataPart;
use Symfony\Contracts\HttpClient\HttpClientInterface;

abstract class Resource
{
    protected HttpClientInterface $httpClient;
    protected string $ariaBaseUrl;
    protected string $ariaApiKey;

    public function __construct(HttpClientInterface $httpClient, string $ariaBaseUrl, string $ariaApiKey)
    {
        $this->httpClient = $httpClient;
        $this->ariaBaseUrl = $ariaBaseUrl;
        $this->ariaApiKey = $ariaApiKey;
    }

    protected function sendGetRequest(string $path): array
    {
        $data = $this->httpClient->request(
            'GET',
            $this->ariaBaseUrl . $path,
            [
                'headers' => ['X-API-Key' => $this->ariaApiKey],
            ]
        );

        $response = $data->toArray(false);

        $this->checkResponseError($response);

        return $response;
    }

    protected function sendPostOrPatchRequest(string $method, string $path, array $body = []): array
    {
        $allowedMethods = [Request::METHOD_POST, Request::METHOD_PATCH];
        if (!in_array($method, $allowedMethods)) {
            throw new \InvalidArgumentException(sprintf('Allowed http methods for function sendPostOrPatchRequest are %s', implode(', ', $allowedMethods)));
        }

        $data = $this->httpClient->request(
            $method,
            $this->ariaBaseUrl . $path,
            [
                'json' => $body,
                'headers' => ['X-API-Key' => $this->ariaApiKey],
            ]
        );

        $response = $data->toArray(false);

        $this->checkResponseError($response);

        return $response;
    }

    protected function sendRequestFormData(string $method, string $path, array $body): array
    {
        $allowedMethods = [Request::METHOD_POST, Request::METHOD_PUT];
        if (!in_array($method, $allowedMethods)) {
            throw new \InvalidArgumentException(sprintf('Allowed http methods for function sendRequestFormData are %s', implode(', ', $allowedMethods)));
        }

        $formData = new FormDataPart($body);

        $data = $this->httpClient->request(
            $method,
            $this->ariaBaseUrl . $path,
            [
                'headers' => array_merge(
                    $formData->getPreparedHeaders()->toArray(),
                    ['X-API-Key' => $this->ariaApiKey]
                ),
                'body' => $formData->bodyToIterable(),
            ]
        );

        $response = $data->toArray(false);

        $this->checkResponseError($response);

        return $response;
    }

    protected function checkResponseError(array $response): void
    {
        if (isset($response['status']) && isset($response['message']) && isset($response['code'])) {
            throw new BadRequestException(sprintf('Error %s %s: %s', $response['status'], $response['code'], $response['message']));
        }
    }
}
