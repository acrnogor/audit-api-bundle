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
                ->arrayNode('entity_classmap')
                    ->info('Map entity FQCN to its shortname (matched via router in URL to make URLs friendly).')
                    ->example(['App\Entity\User' => 'user', 'App\Entity\FeatureFlag' => 'feature-flag'])
                    ->useAttributeAsKey('class')
                    ->normalizeKeys(false)
                    ->scalarPrototype()
                        ->info('The shortname for the entity.')
                        ->cannotBeEmpty()
                    ->end()
                ->end()
            ->end();
    }

    /**
     * @param array{enabled: bool, entity_classmap: array<class-string, string>} $config
     */
    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->parameters()
            ->set('acrnogor_audit_api.enabled', $config['enabled'])
            ->set('acrnogor_audit_api.entity_classmap', $config['entity_classmap']);

        $container->import('../config/services.php');
    }
}
