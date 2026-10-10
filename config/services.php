<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Acrnogor\AuditApiBundle\Controller\GetAudit;
use Acrnogor\AuditApiBundle\Controller\ListAuditHistory;
use Acrnogor\AuditApiBundle\Resolver\EntityClassResolver;
use Acrnogor\AuditApiBundle\Serializer\EntryNameConverter;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

return static function (ContainerConfigurator $container): void {
    $services = $container->services()
        ->defaults()
            ->autowire()
            ->autoconfigure()
            ->private();

    $services->set(ListAuditHistory::class)
        ->public()
        ->tag('controller.service_arguments')
        ->arg('$limit', param('acrnogor_audit_api.pagination.limit'))
        ->arg('$maxLimit', param('acrnogor_audit_api.pagination.max_limit'))
        ->arg('$normalizer', service('acrnogor_audit_api.serializer'));

    // Bundle-private serializer, so we don't depend on (or get affected by) the host app's serializer config.
    $services->set('acrnogor_audit_api.serializer', Serializer::class)
        ->args([[
            inline_service(DateTimeNormalizer::class),
            inline_service(ObjectNormalizer::class)
                ->arg('$nameConverter', inline_service(EntryNameConverter::class)),
        ]]);

    $services->set(GetAudit::class)
        ->public()
        ->tag('controller.service_arguments')
        ->arg('$normalizer', service('acrnogor_audit_api.serializer'));

    $services->set(EntityClassResolver::class)
        ->arg('$entityClassmap', param('acrnogor_audit_api.entity_classmap'));
};
