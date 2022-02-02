<?php

namespace Medelse\AriaBundle\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class BearerGenerator
{
    public const BEARER_GENERATOR_URL = 'https://auth.helloaria.eu/oauth/token';
    public const CACHE_PREFIX_KEY = 'aria-bearer';

    private HttpClientInterface $httpClient;
    private CacheInterface $cache;

    private string $clientId;
    private string $clientSecret;
    private string $ariaAudience;

    public function __construct(HttpClientInterface $httpClient, CacheInterface $cache, string $clientId, string $clientSecret, string $ariaAudience)
    {
        $this->httpClient = $httpClient;
        $this->cache = $cache;

        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->ariaAudience = $ariaAudience;
    }

    public function getBearerToken(): ?string
    {
        $cache = $this->cache->get(
            self::CACHE_PREFIX_KEY,
            function (ItemInterface $item) {
                $response = $this->httpClient->request(
                    Request::METHOD_POST,
                    self::BEARER_GENERATOR_URL,
                    [
                        'json' => [
                            'client_id' => $this->clientId,
                            'client_secret' => $this->clientSecret,
                            'audience' => $this->ariaAudience,
                            'grant_type' => 'client_credentials',
                        ]
                    ]
                );

                $credentials = $response->toArray();
                if (empty($credentials['access_token']) || empty($credentials['expires_in'])) {
                    throw new \InvalidArgumentException('Bearer token cannot be retrieved from response');
                }

                $item
                    ->set($credentials)
                    ->expiresAfter($credentials['expires_in']);

                return $credentials['access_token'];
            }
        );

        return $cache;
    }
}
