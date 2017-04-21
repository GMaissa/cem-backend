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

namespace CEM\Infrastructure\VirtualMachineBundle\Tests\Repository;

use CEM\Infrastructure\VirtualMachineBundle\Factory\Ec2VmFactory;
use PHPUnit\Framework\TestCase;
use CEM\Domain\VirtualMachine\Model\VirtualMachine;
use CEM\Infrastructure\VirtualMachineBundle\Repository\Ec2VmRepository;
use CEM\Infrastructure\VirtualMachineBundle\Tester\Client\Ec2ClientMock;

/**
 * Test class for Ec2 virtual machine repository
 */
class Ec2VmRepositoryTest extends TestCase
{
    private $repository;

    protected function setUp()
    {
        $eventDispatcher = $this->getMockBuilder('\Symfony\Component\EventDispatcher\EventDispatcherInterface')
            ->disableOriginalConstructor()
            ->setMethods([
                'dispatch', 'addListener', 'addSubscriber', 'removeListener', 'removeSubscriber',
                'getListeners', 'getListenerPriority','hasListeners'
            ])
            ->getMock();
        $vmFactory = new Ec2VmFactory();
        $vmFactory->setVmClass('CEM\Domain\VirtualMachine\Model\VirtualMachine');
        $vmFactory->setEventDispatcher($eventDispatcher);

        $this->repository = new Ec2VmRepository();
        $this->repository->setApiClient(new Ec2ClientMock());
        $this->repository->setVmFactory($vmFactory);
        $this->repository->setEventDispatcher($eventDispatcher);
    }

    /**
     * @dataProvider provideFindAll
     */
    public function testFindAll($expectedResult)
    {
        $result = $this->repository->findAll();

        $this->assertEquals($expectedResult, $result);
    }

    public function provideFindAll()
    {
        return [
            [
                [
                    new VirtualMachine(
                        [
                            "id" => "i-00000001",
                            "name" => "Test 1",
                            "type" => "t2.nano",
                            "state" => VirtualMachine::STATE_STOPPED,
                            "az" => "us-west-2b",
                            "privateIp" => "10.0.0.1",
                            "publicIp" => "",
                            "mailingList" => [
                                "projet-test1@test.com"
                            ],
                            "keepAlive" => false,
                            "autoStart" => true,
                            "isInBookmarks" => false
                        ]
                    ),
                    new VirtualMachine(
                        [
                            "id" => "i-00000002",
                            "name" => "Test 2",
                            "type" => "t2.nano",
                            "state" => VirtualMachine::STATE_STARTED,
                            "az" => "us-west-2b",
                            "privateIp" => "10.0.0.2",
                            "publicIp" => "192.168.1.2",
                            "mailingList" => [
                                "projet-test2@test.com"
                            ],
                            "keepAlive" => false,
                            "autoStart" => true,
                            "isInBookmarks" => false
                        ]
                    )
                ]
            ]
        ];
    }

    /**
     * @dataProvider provideFindBy
     */
    public function testFindBy($filters, $expectedResult)
    {
        $result = $this->repository->findBy($filters);

        $this->assertEquals($expectedResult, $result);
    }

    public function provideFindBy()
    {
        return [
            [
                ['keepAlive' => true],
                [
                    new VirtualMachine(
                        [
                            "id" => "i-00000001",
                            "name" => "Test 1",
                            "type" => "t2.nano",
                            "state" => VirtualMachine::STATE_STOPPED,
                            "az" => "us-west-2b",
                            "privateIp" => "10.0.0.1",
                            "publicIp" => "",
                            "mailingList" => [
                                "projet-test1@test.com"
                            ],
                            "keepAlive" => false,
                            "autoStart" => true,
                            "isInBookmarks" => false
                        ]
                    )
                ]
            ],
            [
                ['autoStart' => true],
                [
                    new VirtualMachine(
                        [
                            "id" => "i-00000002",
                            "name" => "Test 2",
                            "type" => "t2.nano",
                            "state" => VirtualMachine::STATE_STARTED,
                            "az" => "us-west-2b",
                            "privateIp" => "10.0.0.2",
                            "publicIp" => "192.168.1.2",
                            "mailingList" => [
                                "projet-test2@test.com"
                            ],
                            "keepAlive" => false,
                            "autoStart" => true,
                            "isInBookmarks" => false
                        ]
                    )
                ]
            ],
            [
                ['states' => [VirtualMachine::STATE_STARTED]],
                [
                    new VirtualMachine(
                        [
                            "id" => "i-00000002",
                            "name" => "Test 2",
                            "type" => "t2.nano",
                            "state" => VirtualMachine::STATE_STARTED,
                            "az" => "us-west-2b",
                            "privateIp" => "10.0.0.2",
                            "publicIp" => "192.168.1.2",
                            "mailingList" => [
                                "projet-test2@test.com"
                            ],
                            "keepAlive" => false,
                            "autoStart" => true,
                            "isInBookmarks" => false
                        ]
                    )
                ]
            ],
            [
                ['vmTypes' => ['development']],
                [
                    new VirtualMachine(
                        [
                            "id" => "i-00000001",
                            "name" => "Test 1",
                            "type" => "t2.nano",
                            "state" => VirtualMachine::STATE_STOPPED,
                            "az" => "us-west-2b",
                            "privateIp" => "10.0.0.1",
                            "publicIp" => "",
                            "mailingList" => [
                                "projet-test1@test.com"
                            ],
                            "keepAlive" => false,
                            "autoStart" => true,
                            "isInBookmarks" => false
                        ]
                    )
                ]
            ],
            [
                ['vmIds' => ['i-00000001']],
                [
                    new VirtualMachine(
                        [
                            "id" => "i-00000001",
                            "name" => "Test 1",
                            "type" => "t2.nano",
                            "state" => VirtualMachine::STATE_STOPPED,
                            "az" => "us-west-2b",
                            "privateIp" => "10.0.0.1",
                            "publicIp" => "",
                            "mailingList" => [
                                "projet-test1@test.com"
                            ],
                            "keepAlive" => false,
                            "autoStart" => true,
                            "isInBookmarks" => false
                        ]
                    )
                ]
            ]
        ];
    }

    /**
     * @dataProvider provideFindException
     */
    public function testFindException($instanceId, $expectedException)
    {
        $this->expectException($expectedException);
        $this->repository->find($instanceId);
    }

    public function provideFindException()
    {
        return [
            [
                'i-00000003',
                '\CEM\Domain\VirtualMachine\Exception\VmNotFoundException'
            ]
        ];
    }

    /**
     * @dataProvider provideFind
     */
    public function testFind($instanceId, $expectedResult)
    {
        $result = $this->repository->find($instanceId);

        $this->assertEquals($expectedResult, $result);
    }

    public function provideFind()
    {
        return [
            [
                'i-00000001',
                new VirtualMachine(
                    [
                        "id" => "i-00000001",
                        "name" => "Test 1",
                        "type" => "t2.nano",
                        "state" => VirtualMachine::STATE_STOPPED,
                        "az" => "us-west-2b",
                        "privateIp" => "10.0.0.1",
                        "publicIp" => "",
                        "mailingList" => [
                            "projet-test1@test.com"
                        ],
                        "keepAlive" => false,
                        "autoStart" => true,
                        "isInBookmarks" => false
                    ]
                )
            ],
            [
                'i-00000002',
                new VirtualMachine(
                    [
                        "id" => "i-00000002",
                        "name" => "Test 2",
                        "type" => "t2.nano",
                        "state" => VirtualMachine::STATE_STARTED,
                        "az" => "us-west-2b",
                        "privateIp" => "10.0.0.2",
                        "publicIp" => "192.168.1.2",
                        "mailingList" => [
                            "projet-test2@test.com"
                        ],
                        "keepAlive" => false,
                        "autoStart" => true,
                        "isInBookmarks" => false
                    ]
                )
            ]
        ];
    }

    /**
     * @dataProvider provideUpdateState
     */
    public function testSave($instanceId, $action, $expectedResults)
    {
        $vm = $this->repository->find($instanceId);
        $vm->{$action}();
        $this->repository->save($vm);
        foreach ($expectedResults as $method => $result) {
            $this->assertEquals($result, $vm->{$method}());
        }
    }

    public function provideUpdateState()
    {
        return [
            [
                'i-00000001',
                'start',
                [
                    'isStarted' => true,
                    'isStopped' => false,
                    'getPublicIp' => '192.168.1.1'
                ]
            ],
            [
                'i-00000002',
                'stop',
                [
                    'isStarted' => false,
                    'isStopped' => true,
                    'getPublicIp' => ''
                ]
            ]
        ];
    }
}
