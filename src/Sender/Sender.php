<?php

namespace Medelse\AriaBundle\Sender;

use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\Mime\Part\Multipart\FormDataPart;
use Symfony\Contracts\HttpClient\HttpClientInterface;

abstract class Sender
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

    protected function sendPostRequest(string $path, array $body): array
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

        if (isset($response['status']) && isset($response['message']) && isset($response['code'])) {
            throw new BadRequestException(sprintf('Error %s %s: %s', $response['status'], $response['code'], $response['message']));
        }

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

        if (isset($response['status']) && isset($response['message']) && isset($response['code'])) {
            throw new BadRequestException(sprintf('Error %s %s: %s', $response['status'], $response['code'], $response['message']));
        }

        return $response;
    }
}
