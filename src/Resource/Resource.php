<?php

namespace Medelse\AriaBundle\Resource;

use Symfony\Component\HttpFoundation\Exception\BadRequestException;
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

    protected function sendPostRequest(string $path, array $body = []): array
    {
        $data = $this->httpClient->request(
            'POST',
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

    protected function sendPutRequestFormData(string $path, array $body): array
    {
        $formData = new FormDataPart($body);

        $data = $this->httpClient->request(
            'PUT',
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
