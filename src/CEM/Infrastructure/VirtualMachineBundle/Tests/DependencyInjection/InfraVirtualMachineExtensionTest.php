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
                        'vm_dashboard.vm.ec2_client',
                        'vm_dashboard.vm.repository',
                        'vm_dashboard.vm.factory.ec2',
                        'vm_dashboard.vm.repository.ec2',
                        'vm_dashboard.vm.subscriber.notification',
                    ]
                ]
            ],
        ];
    }

}

