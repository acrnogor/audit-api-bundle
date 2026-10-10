<?php

declare(strict_types=1);

namespace Acrnogor\AuditApiBundle\Tests\Functional;

use Acrnogor\AuditApiBundle\Controller\GetAudit;
use Acrnogor\AuditApiBundle\Controller\ListAuditHistory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Routing\RouterInterface;

final class RoutingTest extends KernelTestCase
{
    protected static function getKernelClass(): string
    {
        return TestKernel::class;
    }

    public function testListAuditHistoryRouteIsLoadedFromAttribute(): void
    {
        self::bootKernel();

        /** @var RouterInterface $router */
        $router = self::getContainer()->get('router');
        $route = $router->getRouteCollection()->get('acrnogor_auditapi_listaudithistory__invoke');

        self::assertNotNull($route);
        self::assertSame('/audit/{shortName}/{entityId}/history', $route->getPath());
        self::assertSame(['GET'], $route->getMethods());
        self::assertSame(ListAuditHistory::class, $route->getDefault('_controller'));
    }

    public function testHistoryUrlIsNotShadowedByGetAudit(): void
    {
        self::bootKernel();

        /** @var RouterInterface $router */
        $router = self::getContainer()->get('router');

        self::assertSame(ListAuditHistory::class, $router->match('/audit/user/5/history')['_controller']);
        self::assertSame(GetAudit::class, $router->match('/audit/user/5/42')['_controller']);
    }
}
