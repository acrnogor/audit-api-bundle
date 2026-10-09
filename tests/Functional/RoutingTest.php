<?php

declare(strict_types=1);

namespace Acrnogor\AuditApiBundle\Tests\Functional;

use Acrnogor\AuditApiBundle\Controller\GetAudits;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Routing\RouterInterface;

final class RoutingTest extends KernelTestCase
{
    protected static function getKernelClass(): string
    {
        return TestKernel::class;
    }

    public function testGetAuditsRouteIsLoadedFromAttribute(): void
    {
        self::bootKernel();

        /** @var RouterInterface $router */
        $router = self::getContainer()->get('router');
        $route = $router->getRouteCollection()->get('acrnogor_audit_api_get_audits');

        self::assertNotNull($route);
        self::assertSame('/audit/audits/{shortName}/{id}', $route->getPath());
        self::assertSame(['GET'], $route->getMethods());
        self::assertSame(GetAudits::class, $route->getDefault('_controller'));
    }
}
