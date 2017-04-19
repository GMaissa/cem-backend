<?php
/**
 * File part of the VirtualMachine Dashboard
 *
 * @category  CEM
 * @package   CEM.Infrastructure.VirtualMachineBundle
 * @author    Guillaume Maïssa <pro.g@maissa.fr>
 * @copyright 2017 Guillaume Maïssa
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace CEM\Infrastructure\VirtualMachineBundle\Tests\DependencyInjection;

use CEM\Infrastructure\VirtualMachineBundle\DependencyInjection\InfraVirtualMachineExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\Yaml\Parser;

class InfraVirtualMachineExtensionTest extends TestCase
{
    private $container;
    private $extension;

    public function setUp()
    {
        $this->container = $this->container = new ContainerBuilder(new ParameterBag());
        $this->extension = new InfraVirtualMachineExtension();
        $this->container->registerExtension($this->extension);
    }

    /**
     * @dataProvider providerLoad
     */
    public function testLoad($expectedResults)
    {
        $this->extension->load([], $this->container);

        foreach ($expectedResults['parameters'] as $name => $value) {
            $this->assertTrue(
                $this->container->hasParameter($name)
            );
            $this->assertEquals(
                $value,
                $this->container->getParameter($name)
            );
        }
        foreach ($expectedResults['services'] as $name) {
            $this->assertTrue(
                $this->container->has($name)
            );
        }
    }

    public function providerLoad()
    {
        return [
            [
                [
                    'parameters'    => [],
                    'services' => [
                        'cem_virtual_machine.vm.ec2_client',
                        'cem_virtual_machine.vm.repository',
                        'cem_virtual_machine.vm.factory.ec2',
                        'cem_virtual_machine.vm.repository.ec2',
                        'cem_virtual_machine.vm.subscriber.notification',
                    ]
                ]
            ],
        ];
    }

}

