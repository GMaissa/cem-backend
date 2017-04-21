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

namespace CEM\Infrastructure\VirtualMachineBundle\Tests\EventSubscriber;

use CEM\Domain\VirtualMachine\Model\VirtualMachineInterface;
use CEM\Infrastructure\VirtualMachineBundle\Event\VmStateUpdateEvent;
use CEM\Infrastructure\VirtualMachineBundle\EventSubscriber\VmNotificationSubscriber;
use PHPUnit\Framework\TestCase;
use CEM\Domain\VirtualMachine\Model\VirtualMachine;

/**
 * Test class VirtualMachine Notification Event Subscriber
 */
class VmNotificationSubscriberTest extends TestCase
{
    private $mailerService;
    private $message;
    private $tokenStorage;
    private $tplEngine;

    protected function setUp()
    {
        $this->mailerService = $this->getMockBuilder('\CEM\Infrastructure\MailBundle\Service\MailerService')
                                    ->disableOriginalConstructor()
                                    ->setMethods(['createMessage', 'send'])
                                    ->getMock();

        $this->message = $this->getMockBuilder('\Swift_Message')->disableOriginalConstructor()->setMethods(
                [
                    'setSubject',
                    'setFrom',
                    'setTo',
                    'setCc',
                    'setBody',
                    'getHeaders',
                    'addTextHeader',
                ]
            )->getMock();

        $this->tplEngine = $this->getMockBuilder('\Symfony\Bundle\FrameworkBundle\Templating\EngineInterface')
                                ->disableOriginalConstructor()
                                ->setMethods(
                                    [
                                        'render',
                                        'renderResponse',
                                        'exists',
                                        'supports'
                                    ]
                                )
                                ->getMock();

        $this->tokenStorage =
            $this->getMockBuilder('\Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface')
                 ->disableOriginalConstructor()
                 ->setMethods(['getToken', 'getUser', 'setToken'])
                 ->getMock();
    }

    public function testGetSubscribedEvents()
    {
        $subscriber = new VmNotificationSubscriber();

        $this->assertEquals(
            [VmStateUpdateEvent::NAME => [['notifyVmStateChange', 10]]],
            $subscriber->getSubscribedEvents()
        );
    }

    /**
     * @dataProvider providerNotifyVmStateChange
     */
    public function testNotifyVmStateChange(VirtualMachineInterface $virtualMachine, $user, $msgSubject, $tpl, $msgHeader)
    {
        $event = new VmStateUpdateEvent($virtualMachine);

        // Configure security token storage mock
        if (is_null($user)) {
            $this->tokenStorage->expects($this->once())->method('getToken')->willReturn(null);
        } else {
            $this->tokenStorage->expects($this->any())->method('getToken')->willReturn($this->tokenStorage);
            $this->tokenStorage->expects($this->any())->method('getUser')->willReturn($user);
        }

        // configure template engine mock
        $this->tplEngine
            ->expects($this->once())
            ->method('render')
            ->with(
                $tpl,
                [
                    'instance' => $virtualMachine,
                    'user' => $user
                ]
            )
            ->willReturn($tpl);

        $this->mailerService->expects($this->once())->method('createMessage')->willReturn($this->message);

        // Configure message mock
        $this->message->expects($this->once())->method('setSubject')->with($msgSubject)->willReturn($this->message);
        $this->message->expects($this->once())->method('setFrom')->with('notify@test.com')->willReturn($this->message);
        $this->message->expects($this->once())->method('setTo')->with($virtualMachine->getMailingList())->willReturn($this->message);
        $this->message->expects($this->once())->method('setCc')->with('cc@test.com')->willReturn($this->message);
        $this->message->expects($this->once())->method('setBody')->with($tpl, 'text/plain')->willReturn($this->message);
        $this->message->expects($this->once())->method('getHeaders')->willReturn($this->message);
        $this->message->expects($this->once())->method('addTextHeader')->with('X-Message-ID', $msgHeader)->willReturn($this->message);

        // configure mailer service mock
        $this->mailerService->expects($this->once())->method('send')->with($this->message);

        $subscriber = new VmNotificationSubscriber(
            [
                'from' => 'notify@test.com',
                'cc' => 'cc@test.com',
            ]
        );
        $subscriber->setMailerService($this->mailerService);
        $subscriber->setTplEngine($this->tplEngine);
        $subscriber->setSecurityTokenStorage($this->tokenStorage);
        $subscriber->notifyVmStateChange($event);
    }

    public function providerNotifyVmStateChange()
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
                null,
                'Novactive Hosting : Project platform Test 1 stopped',
                'CemVirtualMachineBundle:emails:instanceStopped.text.twig',
                'vm-i-00000001-stopped'
            ],
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
                new \stdClass(),
                'Novactive Hosting : Project platform Test 1 stopped',
                'CemVirtualMachineBundle:emails:instanceStopped.text.twig',
                'vm-i-00000001-stopped'
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
                ),
                null,
                'Novactive Hosting : Project platform Test 2 started',
                'CemVirtualMachineBundle:emails:instanceStarted.text.twig',
                'vm-i-00000002-started'
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
                ),
                new \stdClass(),
                'Novactive Hosting : Project platform Test 2 started',
                'CemVirtualMachineBundle:emails:instanceStarted.text.twig',
                'vm-i-00000002-started'
            ]
        ];
    }
}

