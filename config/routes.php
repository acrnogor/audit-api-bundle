<?php

declare(strict_types=1);

use Acrnogor\AuditApiBundle\Controller\GetAudits;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $routes): void {
    $routes->add('acrnogor_audit_api_hello', '/hello')
        ->controller(GetAudits::class)
        ->methods(['GET']);
};
