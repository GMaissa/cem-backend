<?php
/**
 * File part of the Cloud Environments Management Backend
 *
 * @category  CEM
 * @package   CEM.Infrastructure.VirtualMachineBundle
 * @author    Guillaume Maïssa <pro.g@maissa.fr>
 * @copyright 2017 Guillaume Maïssa
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace CEM\Infrastructure\VirtualMachineBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Bundle configuration management
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder      = new TreeBuilder();
        $rootNode         = $treeBuilder->root('cem_virtual_machine');

        $rootNode
            ->children()
                ->enumNode('cloud_provider')->values(array('ec2'))->defaultValue('ec2')->end()
                ->arrayNode('notification')
                    ->children()
                        ->scalarNode('from')->isRequired()->cannotBeEmpty()->end()
                        ->scalarNode('cc')->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
