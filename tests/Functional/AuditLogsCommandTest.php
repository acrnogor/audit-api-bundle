<?php

declare(strict_types=1);

namespace Acrnogor\AuditApiBundle\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class AuditLogsCommandTest extends KernelTestCase
{
    protected static function getKernelClass(): string
    {
        return TestKernel::class;
    }

    public function testCommandIsRegistered(): void
    {
        $application = new Application(self::bootKernel());

        self::assertTrue($application->has('audit:logs'));
    }
}
