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

namespace CEM\Infrastructure\VirtualMachineBundle\Tests\Event;

use CEM\Infrastructure\VirtualMachineBundle\Event\VmPostLoadEvent;
use PHPUnit\Framework\TestCase;
use CEM\Domain\VirtualMachine\Model\VirtualMachine;

/**
 * Test class for VirtualMachine Events
 */
class VmEventsTest extends TestCase
{
    /**
     * @dataProvider provideFindAll
     */
    public function testEvent($expectedResult)
    {
        $event = new VmPostLoadEvent($expectedResult);

        $this->assertEquals($expectedResult, $event->getVm());
    }

    public function provideFindAll()
    {
        return [
            [
                new VirtualMachine(
                    [
                        "id"            => "i-00000001",
                        "name"          => "Test 1",
                        "type"          => "t2.nano",
                        "state"         => VirtualMachine::STATE_STOPPED,
                        "az"            => "us-west-2b",
                        "privateIp"     => "10.0.0.1",
                        "publicIp"      => "",
                        "mailingList"   => [
                            "projet-test1@test.com"
                        ],
                        "keepAlive"     => false,
                        "autoStart"     => true,
                        "isInBookmarks" => false
                    ]
                ),
            ],
            [
                new VirtualMachine(
                    [
                        "id"            => "i-00000002",
                        "name"          => "Test 2",
                        "type"          => "t2.nano",
                        "state"         => VirtualMachine::STATE_STARTED,
                        "az"            => "us-west-2b",
                        "privateIp"     => "10.0.0.2",
                        "publicIp"      => "192.168.1.2",
                        "mailingList"   => [
                            "projet-test2@test.com"
                        ],
                        "keepAlive"     => false,
                        "autoStart"     => true,
                        "isInBookmarks" => false
                    ]
                )
            ]
        ];
    }
}
