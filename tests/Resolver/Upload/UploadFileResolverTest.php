<?php

namespace Medelse\AriaBundle\Tests\Resolver\Upload;

use Medelse\AriaBundle\Resolver\Upload\UploadFileResolver;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;

class UploadFileResolverTest extends TestCase
{
    public function testResolve()
    {
        $fileData = [
            'document' => 'zombies_invoice.pdf',
            'contentType' => 'application/pdf',
        ];

        $resolver = new UploadFileResolver();
        $data = $resolver->resolve($fileData);

        $this->assertIsArray($data);
        $this->assertTrue($data['file'] instanceof DataPart);
        $this->assertSame('application', $data['file']->getMediaType());
        $this->assertSame('pdf', $data['file']->getMediaSubtype());
    }

    public function testResolveBadType()
    {
        $fileData = [
            'document' => 'zombies_invoice.toto',
            'contentType' => 'application/toto',
        ];

        $this->expectException(InvalidOptionsException::class);
        $this->expectExceptionMessage('The option "contentType" with value "application/toto" is invalid');

        $resolver = new UploadFileResolver();
        $resolver->resolve($fileData);
    }
}
