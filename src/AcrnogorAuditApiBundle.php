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
                ->arrayNode('pagination')
                    ->info('Pagination settings for audit listing endpoints.')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->integerNode('limit')
                            ->defaultValue(10)
                            ->min(1)
                            ->info('Default number of items per page.')
                        ->end()
                        ->integerNode('max_limit')
                            ->defaultValue(100)
                            ->min(1)
                            ->info('Maximum number of items per page a client may request.')
                        ->end()
                    ->end()
                    ->validate()
                        ->ifTrue(static fn (array $v): bool => $v['limit'] > $v['max_limit'])
                        ->thenInvalid('"limit" must be less than or equal to "max_limit".')
                    ->end()
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
     * @param array{enabled: bool, pagination: array{limit: int, max_limit: int}, entity_classmap: array<class-string, string>} $config
     */
    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->parameters()
            ->set('acrnogor_audit_api.enabled', $config['enabled'])
            ->set('acrnogor_audit_api.pagination.limit', $config['pagination']['limit'])
            ->set('acrnogor_audit_api.pagination.max_limit', $config['pagination']['max_limit'])
            ->set('acrnogor_audit_api.entity_classmap', $config['entity_classmap']);

        $container->import('../config/services.php');
    }
}
