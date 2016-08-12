<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 23.07.16
 * Time: 13:52
 */

namespace Venice\AppBundle\DependencyInjection;


use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{

    /**
     * Generates the configuration tree builder.
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder The tree builder
     * @throws \RuntimeException
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('venice_app');

        $mappingNode = $rootNode->children()->arrayNode('entity_mapping')->useAttributeAsKey('name');
        $mappingNode->prototype('array')->children()
            ->scalarNode('entity')->isRequired()->end()
            ->arrayNode('map')->useAttributeAsKey('name')->prototype('scalar');

        $rootNode->children()
            ->arrayNode('forms_override')
            ->defaultValue([])
            ->useAttributeAsKey('venice_forms_override')
            ->prototype('scalar');

        $rootNode->children()
            ->arrayNode('entity_forms')
            ->defaultValue([])
            ->useAttributeAsKey('venice_entity_forms')
            ->prototype('scalar');

        $rootNode->children()
            ->arrayNode('entity_override')
            ->defaultValue([])
            ->useAttributeAsKey('venice_entity_override')
            ->prototype('scalar');

        return $treeBuilder;
    }
}
