<?php
/**
 * File part of the VirtualMachine Dashboard
 *
 * @category  CEM
 * @package   CEM.Domain.VirtualMachine
 * @author    Guillaume Maïssa <pro.g@maissa.fr>
 * @copyright 2017 Guillaume Maïssa
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
