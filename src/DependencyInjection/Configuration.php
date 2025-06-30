<?php

declare(strict_types=1);

namespace Wemxo\FilerBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('filer');

        $treeBuilder->getRootNode()
            ->fixXmlConfig('type')
            ->children()
                ->arrayNode('types')
                    ->defaultValue([])
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('folder')->isRequired()->end()
                            ->scalarNode('access')->isRequired()->end()
                            ->scalarNode('source')->defaultNull()->end()
                            ->integerNode('max_size')->isRequired()->end()
                            ->booleanNode('apply_watermarK')->defaultFalse()->end()
                            ->booleanNode('keep_source')
                                ->defaultTrue()
                            ->end()
                            ->arrayNode('mime_types')
                                ->isRequired()
                                ->requiresAtLeastOneElement()
                                ->scalarPrototype()->end()
                            ->end()
                            ->arrayNode('filters')
                                ->defaultValue([])
                                ->scalarPrototype()->end()
                            ->end()
                        ->end()
                        ->validate()
                            ->ifTrue(function ($config) {
                                return empty($config['filters']) && !$config['keep_source'];
                            })
                            ->thenInvalid('keep_source should not be FALSE when no filter specified.')
                        ->end()
                        ->validate()
                            ->ifTrue(function ($config) {
                                return !empty($config['source']) && $config['keep_source'];
                            })
                            ->thenInvalid('source should not be defined when keep_source is TRUE.')
                        ->end()
                        ->validate()
                            ->ifTrue(function ($config) {
                                return !$config['keep_source'] && !empty($config['filters']) && !empty($config['source']) && !in_array($config['source'], $config['filters']);
                            })
                            ->thenInvalid('specified source should be one of configured filters.')
                        ->end()
                        ->validate()
                            ->ifTrue(function ($config) {
                                return !$config['keep_source'] && empty($config['source']);
                            })
                            ->thenInvalid('source should be specified when keep_source is FALSE.')
                        ->end()
                    ->end()
                ->end()
            ->end()
            ->fixXmlConfig('access')
            ->children()
                ->arrayNode('accesses')
                    ->defaultValue([])
                    ->scalarPrototype()->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
