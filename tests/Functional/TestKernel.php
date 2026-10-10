<?php

declare(strict_types=1);

namespace Acrnogor\AuditApiBundle\Tests\Functional;

use Acrnogor\AuditApiBundle\AcrnogorAuditApiBundle;
use DH\Auditor\Provider\Doctrine\Persistence\Reader\Reader;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\NullLogger;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

final class TestKernel extends Kernel
{
    use MicroKernelTrait;

    public function registerBundles(): iterable
    {
        yield new FrameworkBundle();
        yield new AcrnogorAuditApiBundle();
    }

    public function getProjectDir(): string
    {
        return __DIR__.'/app';
    }

    public function getCacheDir(): string
    {
        return sys_get_temp_dir().'/acrnogor_audit_api_bundle/cache/'.$this->environment;
    }

    public function getLogDir(): string
    {
        return sys_get_temp_dir().'/acrnogor_audit_api_bundle/logs';
    }

    protected function configureContainer(ContainerConfigurator $container): void
    {
        $container->extension('framework', [
            'test' => true,
            'secret' => 'test',
            'http_method_override' => false,
            'handle_all_throwables' => true,
            'php_errors' => ['log' => true],
        ]);

        $services = $container->services();
        $services->set('logger', NullLogger::class);

        // Doctrine is provided by the host app; stub it so the container compiles.
        $services->set(EntityManagerInterface::class)->synthetic();
        $services->set(Connection::class)->synthetic();

        // Provided by DHAuditorBundle in the host app.
        $services->set(Reader::class)->synthetic();
    }

    protected function configureRoutes(RoutingConfigurator $routes): void
    {
        $routes->import(\dirname(__DIR__, 2).'/src/Controller/', 'attribute')->prefix('/audit');
    }
}
