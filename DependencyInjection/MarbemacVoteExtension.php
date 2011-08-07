<?php

namespace Marbemac\VoteBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Definition;

class MarbemacVoteExtension extends Extension
{
    protected $resources = array(
        'manager' => 'manager.xml'
    );

    public function load(array $configs, ContainerBuilder $container)
    {
        $this->loadDefaults($container);
        
        $processor = new Processor();
        $configuration = new Configuration();
        $config = $processor->processConfiguration($configuration, $configs);

        $variables = array(
                        'vote_manager',
                        'document_stem'
                    );

        foreach ($variables as $attribute) {
            $container->setParameter('marbemac_vote.options.'.$attribute, $config[$attribute]);
        }
    }

    /**
     * @codeCoverageIgnore
     */
    public function getNamespace()
    {
        return 'http://symfony.com/schema/dic/marbemac_vote';
    }

    /**
     * @codeCoverageIgnore
     */
    protected function loadDefaults($container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        
        foreach ($this->resources as $resource) {
            $loader->load($resource);
        }
    }
}