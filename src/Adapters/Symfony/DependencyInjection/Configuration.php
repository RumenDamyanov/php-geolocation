<?php

/**
 * Configuration class for Symfony adapter.
 *
 * Defines the configuration tree for geolocation settings.
 *
 * @category Geolocation
 * @package  Rumenx\Geolocation\Adapters\Symfony
 * @author   Rumen Damyanov <contact@rumenx.com>
 * @license  https://opensource.org/licenses/MIT MIT License
 * @link     https://github.com/RumenDamyanov/php-geolocation
 */

namespace Rumenx\Geolocation\Adapters\Symfony\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 *
 * Provides configuration tree builder for geolocation.
 */
class Configuration implements ConfigurationInterface
{
    /**
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('geolocation');
        $rootNode = $treeBuilder->getRootNode();
        $rootNode
            ->children()
            ->arrayNode('country_to_language')->prototype('variable')->end()->end()
            ->scalarNode('default_language')->defaultValue('en')->end()
            ->scalarNode('language_cookie')->defaultValue('lang')->end()
            ->end();
        return $treeBuilder;
    }
}
