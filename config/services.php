<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Acrnogor\AuditApiBundle\Controller\GetAudit;
use Acrnogor\AuditApiBundle\Controller\ListAuditHistory;
use Acrnogor\AuditApiBundle\Repository\AuditRepository;
use Acrnogor\AuditApiBundle\Resolver\EntityClassResolver;

return static function (ContainerConfigurator $container): void {
    $services = $container->services()
        ->defaults()
            ->autowire()
            ->autoconfigure()
            ->private();

    $services->set(ListAuditHistory::class)
        ->public()
        ->tag('controller.service_arguments')
        ->arg('$defaultLimit', param('acrnogor_audit_api.pagination.limit'))
        ->arg('$maxLimit', param('acrnogor_audit_api.pagination.max_limit'));

    $services->set(GetAudit::class)
        ->public()
        ->tag('controller.service_arguments');

    $services->set(EntityClassResolver::class)
        ->arg('$entityClassmap', param('acrnogor_audit_api.entity_classmap'));

    $services->set(AuditRepository::class);
};
