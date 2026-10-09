<?php

declare(strict_types=1);

namespace Acrnogor\AuditApiBundle\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class BundleInitializationTest extends KernelTestCase
{
    protected static function getKernelClass(): string
    {
        return TestKernel::class;
    }

    public function testBundleBoots(): void
    {
        self::bootKernel();

        self::assertTrue(self::getContainer()->getParameter('acrnogor_audit_api.enabled'));
    }
}
