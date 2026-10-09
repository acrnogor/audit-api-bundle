<?php

declare(strict_types=1);

namespace Acrnogor\AuditApiBundle;

use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

final class AcrnogorAuditApiBundle extends AbstractBundle
{
    protected string $extensionAlias = 'acrnogor_audit_api';

    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->rootNode()
            ->children()
                ->booleanNode('enabled')
                    ->defaultTrue()
                    ->info('Whether the audit API is enabled.')
                ->end()
            ->end();
    }

    /**
     * @param array{enabled: bool} $config
     */
    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->parameters()->set('acrnogor_audit_api.enabled', $config['enabled']);

        $container->import('../config/services.php');
    }
}
