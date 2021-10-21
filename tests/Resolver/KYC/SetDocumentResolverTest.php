<?php

namespace Medelse\AriaBundle\Tests\Resolver\KYC;

use Medelse\AriaBundle\Resolver\KYC\SetDocumentResolver;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;

class SetDocumentResolverTest extends TestCase
{
    public function testResolve()
    {
        $document = $this->getDocument();

        $resolver = new SetDocumentResolver();
        $data = $resolver->resolve($document);

        $this->assertIsArray($data);
        $this->assertArrayHasKey('document', $data);
        $this->assertTrue($data['document'] instanceof DataPart);
    }

    public function testBadMimeTypeValue()
    {
        $document = $this->getDocument();
        $document['mimeType'] = 'wrong/mimeType';

        $resolver = new SetDocumentResolver();
        $this->expectException(InvalidOptionsException::class);
        $resolver->resolve($document);
    }

    /**
     *
     * PRIVATE
     *
     */

    private function getDocument(): array
    {
        return [
            'document' => 'string',
            'fileName' => 'string',
            'mimeType' => 'image/jpeg',
        ];
    }
}
