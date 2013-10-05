<?php

namespace YourLife\DataBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $treeBuilder->root('your_life_data')
            ->children()
                ->scalarNode('mission_photos_path')
                    ->info('Путь к изображениям миссий')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('mission_result_photos_path')
                    ->info('Путь к изображениям результатов выполнения миссий')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
            ->end();

        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.

        return $treeBuilder;
    }
}
