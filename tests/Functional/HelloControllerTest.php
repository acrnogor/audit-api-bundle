<?php

declare(strict_types=1);

namespace Acrnogor\AuditApiBundle\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class HelloControllerTest extends WebTestCase
{
    protected static function getKernelClass(): string
    {
        return TestKernel::class;
    }

    public function testHelloReturnsJson(): void
    {
        $client = self::createClient();
        $client->request('GET', '/hello');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('Content-Type', 'application/json');
        self::assertJsonStringEqualsJsonString(
            '{"message": "Hello World"}',
            (string) $client->getResponse()->getContent(),
        );
    }
}
