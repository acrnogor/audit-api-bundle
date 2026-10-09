<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Acrnogor\AuditApiBundle\Controller\HelloController;

return static function (ContainerConfigurator $container): void {
    $services = $container->services()
        ->defaults()
            ->autowire()
            ->autoconfigure()
            ->private();

    $services->set(HelloController::class)
        ->public()
        ->tag('controller.service_arguments');
};
