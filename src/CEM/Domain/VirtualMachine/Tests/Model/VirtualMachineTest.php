<?php
/**
 * File part of the VirtualMachine Dashboard
 *
 * @category  CEM
 * @package   CEM.Domain.VirtualMachine
 * @author    Guillaume Maïssa <pro.g@maissa.fr>
 * @copyright 2017 Guillaume Maïssa
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace CEM\Domain\VirtualMachine\Tests\Model;

use CEM\Domain\VirtualMachine\Model\VirtualMachine;
use PHPUnit\Framework\TestCase;

/**
 * Unit test class for virtual machine model
 */
class VirtualMachineTest extends TestCase
{
    /**
     * @dataProvider providerIsStarted
     */
    public function testIsStarted($vmData, $expectedResult)
    {
        $vm = new VirtualMachine($vmData);
        $this->assertEquals($expectedResult, $vm->isStarted());
    }

    public function providerIsStarted()
    {
        $vmData = $this->getVmData();
        return [
            [
                array_merge($vmData, ['state' => VirtualMachine::STATE_STARTED]),
                true
            ],
            [
                array_merge($vmData, ['state' => VirtualMachine::STATE_STOPPED]),
                false
            ],
            [
                array_merge($vmData, ['state' => VirtualMachine::STATE_STARTING]),
                false
            ],
            [
                array_merge($vmData, ['state' => VirtualMachine::STATE_STOPPING]),
                false
            ],
        ];
    }


    /**
     * @dataProvider providerIsStopped
     */
    public function testIsStopped($vmData, $expectedResult)
    {
        $vm = new VirtualMachine($vmData);
        $this->assertEquals($expectedResult, $vm->isStopped());
    }

    public function providerIsStopped()
    {
        $vmData = $this->getVmData();
        return [
            [
                array_merge($vmData, ['state' => VirtualMachine::STATE_STARTED]),
                false
            ],
            [
                array_merge($vmData, ['state' => VirtualMachine::STATE_STOPPED]),
                true
            ],
            [
                array_merge($vmData, ['state' => VirtualMachine::STATE_STARTING]),
                false
            ],
            [
                array_merge($vmData, ['state' => VirtualMachine::STATE_STOPPING]),
                false
            ],
        ];
    }

    /**
     * @dataProvider providerIsStartedOrStopped
     */
    public function testIsStartedOrStopped($vmData, $expectedResult)
    {
        $vm = new VirtualMachine($vmData);
        $this->assertEquals($expectedResult, $vm->isStartedOrStopped());
    }

    public function providerIsStartedOrStopped()
    {
        $vmData = $this->getVmData();
        return [
            [
                array_merge($vmData, ['state' => VirtualMachine::STATE_STARTED]),
                true
            ],
            [
                array_merge($vmData, ['state' => VirtualMachine::STATE_STOPPED]),
                true
            ],
            [
                array_merge($vmData, ['state' => VirtualMachine::STATE_STARTING]),
                false
            ],
            [
                array_merge($vmData, ['state' => VirtualMachine::STATE_STOPPING]),
                false
            ],
        ];
    }

    /**
     * @dataProvider providerCanStart
     */
    public function testCanStart($vmData, $expectedResult)
    {
        $vm = new VirtualMachine($vmData);
        $this->assertEquals($expectedResult, $vm->canStart());
    }

    public function providerCanStart()
    {
        $vmData = $this->getVmData();
        return [
            [
                array_merge($vmData, ['state' => VirtualMachine::STATE_STARTED]),
                false
            ],
            [
                array_merge($vmData, ['state' => VirtualMachine::STATE_STOPPED]),
                true
            ],
            [
                array_merge($vmData, ['state' => VirtualMachine::STATE_STARTING]),
                false
            ],
            [
                array_merge($vmData, ['state' => VirtualMachine::STATE_STOPPING]),
                false
            ],
        ];
    }

    /**
     * @dataProvider providerCanStop
     */
    public function testCanStop($vmData, $expectedResult)
    {
        $vm = new VirtualMachine($vmData);
        $this->assertEquals($expectedResult, $vm->canStop());
    }

    public function providerCanStop()
    {
        $vmData = $this->getVmData();
        return [
            [
                array_merge($vmData, ['state' => VirtualMachine::STATE_STARTED]),
                true
            ],
            [
                array_merge($vmData, ['state' => VirtualMachine::STATE_STOPPED]),
                false
            ],
            [
                array_merge($vmData, ['state' => VirtualMachine::STATE_STARTING]),
                false
            ],
            [
                array_merge($vmData, ['state' => VirtualMachine::STATE_STOPPING]),
                false
            ],
        ];
    }

    /**
     * @dataProvider providerStartException
     */
    public function testStartException($vmData)
    {
        $this->expectException(
            'CEM\Domain\VirtualMachine\Exception\VmStateUpdateException'
        );
        $this->expectExceptionMessage(sprintf('Impossible to start Virtual Machine %s', $vmData['id']));
        $vm = new VirtualMachine($vmData);
        $vm->start();
    }

    public function providerStartException()
    {
        $vmData = $this->getVmData();
        return [
            [array_merge($vmData, ['state' => VirtualMachine::STATE_STARTED])],
            [array_merge($vmData, ['state' => VirtualMachine::STATE_STARTING])],
            [array_merge($vmData, ['state' => VirtualMachine::STATE_STOPPING])],
        ];
    }

    /**
     * @dataProvider providerStart
     */
    public function testStart($vmData, $expectedResult)
    {
        $vm = new VirtualMachine($vmData);
        $vm->start();
        $this->assertEquals($expectedResult, $vm->isStarted());
    }

    public function providerStart()
    {
        $vmData = $this->getVmData();
        return [
            [array_merge($vmData, ['state' => VirtualMachine::STATE_STOPPED]), true]
        ];
    }

    /**
     * @dataProvider providerStopException
     */
    public function testStopException($vmData)
    {
        $this->expectException(
            'CEM\Domain\VirtualMachine\Exception\VmStateUpdateException'
        );
        $this->expectExceptionMessage(sprintf('Impossible to stop Virtual Machine %s', $vmData['id']));
        $vm = new VirtualMachine($vmData);
        $vm->stop();
    }

    public function providerStopException()
    {
        $vmData = $this->getVmData();
        return [
            [array_merge($vmData, ['state' => VirtualMachine::STATE_STOPPED])],
            [array_merge($vmData, ['state' => VirtualMachine::STATE_STARTING])],
            [array_merge($vmData, ['state' => VirtualMachine::STATE_STOPPING])],
        ];
    }
    /**
     * @dataProvider providerStop
     */
    public function testStop($vmData, $expectedResult)
    {
        $vm = new VirtualMachine($vmData);
        $vm->stop();
        $this->assertEquals($expectedResult, $vm->isStopped());
    }

    public function providerStop()
    {
        $vmData = $this->getVmData();
        return [
            [array_merge($vmData, ['state' => VirtualMachine::STATE_STARTED]), true],
        ];
    }

    protected function getVmData()
    {
        return [
            'id' => 'i-12345678',
            'name' => 'VM 1',
            'type' => 'micro',
            'az' => 'eu-west-1',
            'privateIp' => '10.0.0.1',
            'publicIp' => '10.0.0.2',
            'mailingList' => ['test@test.com'],
            'keepAlive' => 0,
            'autoStart' => 1,
            'isInBookmarks' => 0,
        ];
    }
}
