<?php

namespace Marbemac\VoteBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder,
    Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This class contains the configuration information for the bundle
 *
 * This information is solely responsible for how the different configuration
 * sections are normalized, and merged.
 *
 */
class Configuration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree.
     *
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('marbemac_vote');

        $rootNode
            ->children()
                ->scalarNode('vote_manager')->defaultValue('Marbemac\VoteBundle\Document\VoteManager')->cannotBeEmpty()->end()
                ->scalarNode('document_stem')->cannotBeEmpty()->end()
            ->end();

        return $treeBuilder;
    }

}