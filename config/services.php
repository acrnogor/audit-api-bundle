<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function (ContainerConfigurator $container): void {
    $services = $container->services()
        ->defaults()
            ->autowire()
            ->autoconfigure()
            ->private();

    // Register bundle services here, e.g.:
    // $services->set('acrnogor_audit_api.some_service', \Acrnogor\AuditApiBundle\Service\SomeService::class);
};
