<?php

namespace Medelse\AriaBundle\Tests\Resolver\User;

use Medelse\AriaBundle\Resolver\WebHook\CreateWebHookResolver;
use Medelse\AriaBundle\Resource\WebHook;
use PHPUnit\Framework\TestCase;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;

class CreateWebHookResolverTest extends TestCase
{
    public function testResolve()
    {
        $resolver = new CreateWebHookResolver();
        $data = $resolver->resolve($this->getWebHookData());

        $this->assertIsArray($data);

        $this->assertArrayHasKey('options', $data);
        $this->assertIsArray($data['options']);
        $this->assertArrayHasKey('endpoint', $data['options']);
        $this->assertEquals('https://cicada3301.com', $data['options']['endpoint']);
        $this->assertArrayHasKey('secret', $data['options']);
        $this->assertEquals('lxxt>33m2mqkyv2gsq3q=w]O2ntk', $data['options']['secret']);

        $this->assertArrayHasKey('event', $data);
        $this->assertEquals(WebHook::EVENT_CONTRACT_CREATED, $data['event']);
    }

    public function testResolveWithEmptyOptionsEndpointThrowsException()
    {
        $webHookData = $this->getWebHookData();
        unset($webHookData['options']['endpoint']);

        $resolver = new CreateWebHookResolver();
        $this->expectException(InvalidOptionsException::class);
        $this->expectExceptionMessage('Option "options" must have an "endpoint" key and its value must not be empty');
        $data = $resolver->resolve($webHookData);
    }

    public function testResolveWithBadLengthOptionsSecretThrowsException()
    {
        $webHookData = $this->getWebHookData();
        $webHookData['options']['secret'] = 'lxxt';

        $resolver = new CreateWebHookResolver();
        $this->expectException(InvalidOptionsException::class);
        $this->expectExceptionMessage('Key "secret" of option "options" must be null or its length must be between 16 and 128 characters');
        $data = $resolver->resolve($webHookData);
    }

    public function testResolveWithBadEventValueThrowsException()
    {
        $webHookData = $this->getWebHookData();
        $webHookData['event'] = 'message.destroy';

        $resolver = new CreateWebHookResolver();
        $this->expectException(InvalidOptionsException::class);
        $data = $resolver->resolve($webHookData);
    }

    private function getWebHookData(): array
    {
        return [
            'options' => [
                'endpoint' => 'https://cicada3301.com',
                'secret' => 'lxxt>33m2mqkyv2gsq3q=w]O2ntk',
            ],
            'event' => WebHook::EVENT_CONTRACT_CREATED,
        ];
    }
}
