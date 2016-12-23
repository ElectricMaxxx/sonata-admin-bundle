<?php

namespace Symfony\Cmf\Bundle\SonataAdminIntegrationBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Cmf\Bundle\SonataAdminIntegrationBundle\DependencyInjection\Factory\AdminFactoryInterface;

/**
 * @author Maximilian Berghoff <Maximilian.Berghoff@mayflower.de>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * @var AdminFactoryInterface[]
     */
    private $factories;

    public function __construct(array $factories = [])
    {
        $this->factories = $factories;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $root = $treeBuilder->root('cmf_sonata_admin_integration');

        $root
            ->children()
                ->enumNode('persistence')
                    ->values([null, 'phpcr', 'orm'])
                    ->defaultNull()
                ->end()
            ->end()
        ;

        $this->addBundlesSection($root);

        return $treeBuilder;
    }

    private function addBundlesSection(ArrayNodeDefinition $root)
    {
        $bundles = $root->children()->arrayNode('bundles')->children();

        foreach ($this->factories as $factory) {
            $config = $bundles
                ->arrayNode($factory->getKey())
                    ->addDefaultsIfNotSet()
                    ->canBeEnabled()
                    ->children();

            $persistenceConfig = $config
                ->arrayNode('persistence')
                    ->addDefaultsIfNotSet()
                    ->children();

            $factory->addConfiguration($persistenceConfig, $config);
        }
    }
}
