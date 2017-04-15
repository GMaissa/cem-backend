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

namespace CEM\Infrastructure\VirtualMachineBundle\Tests\Repository;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use CEM\Domain\VirtualMachine\Model\VirtualMachine;
use CEM\Infrastructure\VirtualMachineBundle\Repository\Ec2VmRepository;
use CEM\Infrastructure\VirtualMachineBundle\Tester\Client\Ec2ClientMock;

/**
 * Test class for Ec2 virtual machine repository
 */
class Ec2VmRepositoryTest extends KernelTestCase
{
    private $apiClient;
    private $vmFactory;
    private $container;
    private $eventDispatcher;

    protected function setUp()
    {
        parent::setUp();
        self::bootKernel();

        $this->apiClient = new Ec2ClientMock();
        $this->container = static::$kernel->getContainer();
        $this->vmFactory = $this->container->get('vm_dashboard.vm.factory');
        $this->eventDispatcher = $this->getMockBuilder('\Symfony\Component\EventDispatcher\EventDispatcherInterface')
                                      ->disableOriginalConstructor()
                                      ->setMethods([
                                          'dispatch', 'addListener', 'addSubscriber', 'removeListener', 'removeSubscriber',
                                          'getListeners', 'getListenerPriority','hasListeners'
                                      ])
                                      ->getMock();
    }

    /**
     * @dataProvider provideFindAll
     */
    public function testFindAll($expectedResult)
    {
        $repository = $this->initRepository();
        $result = $repository->findAll();

        $this->assertEquals($expectedResult, $result);
    }

    public function provideFindAll()
    {
        return array(
            array(
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
            )
        );
    }

    /**
     * @dataProvider provideFindBy
     */
    public function testFindBy($filters, $expectedResult)
    {
        $repository = $this->initRepository();
        $result = $repository->findBy($filters);

        $this->assertEquals($expectedResult, $result);
    }

    public function provideFindBy()
    {
        return array(
            array(
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
            ),
            array(
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
            ),
            array(
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
            ),
            array(
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
            ),
            array(
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
            )
        );
    }

    /**
     * @dataProvider provideFindException
     */
    public function testFindException($instanceId, $expectedException)
    {
        $this->expectException($expectedException);
        $repository = $this->initRepository();
        $repository->find($instanceId);
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
        $repository = $this->initRepository();
        $result = $repository->find($instanceId);

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
     * @dataProvider provideUpdateStateException
     */
//    public function testUpdateStateException($instanceId, $action, $expectedException)
//    {
//        $this->expectException($expectedException);
//        $repository = $this->initRepository();
//        $vm = $repository->find($instanceId);
//        $repository->updateState($vm, $action);
//    }

    public function provideUpdateStateException()
    {
        return [
            [
                'i-00000001',
                'stop',
                '\Domain\VirtualMachine\Exception\VmStateUpdateException'
            ],
            [
                'i-00000002',
                'start',
                '\Domain\VirtualMachine\Exception\VmStateUpdateException'
            ]
        ];
    }

    /**
     * @dataProvider provideUpdateState
     */
//    public function testUpdateState($instanceId, $action, $expectedResults)
//    {
//        $repository = $this->initRepository();
//        $vm = $repository->find($instanceId);
//        $repository->updateState($vm, $action);
//        foreach ($expectedResults as $method => $result) {
//            $this->assertEquals($result, $vm->{$method}());
//        }
//    }

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
    protected function initRepository()
    {
        $repository = new Ec2VmRepository();
        $repository->setApiClient($this->apiClient);
        $repository->setVmFactory($this->vmFactory);
        $repository->setEventDispatcher($this->eventDispatcher);

        return $repository;
    }
}
