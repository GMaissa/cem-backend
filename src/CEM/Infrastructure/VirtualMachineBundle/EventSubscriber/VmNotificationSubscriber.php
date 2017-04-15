<?php
/**
 * File part of the VirtualMachine Dashboard
 *
 * @category  CEM
 * @package  CEM.Domain.VirtualMachine
 * @author    Guillaume Maïssa <pro.g@maissa.fr>
 * @copyright 2017 Guillaume Maïssa
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace CEM\Infrastructure\VirtualMachineBundle\EventSubscriber;

use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use CEM\Infrastructure\MailBundle\Service\MailerService;
use CEM\Infrastructure\UserBundle\Model\User;
use CEM\Infrastructure\VirtualMachineBundle\Event\VmStateUpdateEvent;

/**
 * VirtualMachine listener for notification
 */
class VmNotificationSubscriber implements EventSubscriberInterface
{
    /**
     * Notification configuration
     * @var array
     */
    private $notificationConfig;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * Mailer object
     * @var MailerService
     */
    private $mailer;

    /**
     * @var EngineInterface
     */
    private $tplEngine;

    /**
     * Class constructor
     *
     * @param array $notificationConfig
     */
    public function __construct($notificationConfig = array())
    {
        $this->notificationConfig = $notificationConfig;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            VmStateUpdateEvent::NAME => array(array('notifyVmStateChange', 10))
        );
    }

    /**
     * Sets the virtual machines provider object
     *
     * @param TokenStorageInterface $securityTokenStorage
     */
    public function setSecurityTokenStorage(TokenStorageInterface $securityTokenStorage)
    {
        $this->tokenStorage = $securityTokenStorage;
    }

    /**
     * Sets the mailer.
     *
     * @param MailerService $mailer
     */
    public function setMailerService(MailerService $mailer = null)
    {
        $this->mailer = $mailer;
    }

    /**
     * Sets the template engine
     *
     * @param EngineInterface $tplEngine
     */
    public function setTplEngine(EngineInterface $tplEngine)
    {
        $this->tplEngine = $tplEngine;
    }

    /**
     * Get currently logged in user
     *
     * @return null|User
     */
    protected function getCurrentUser()
    {
        $user = null;
        if (!is_null($this->tokenStorage->getToken())) {
            $user = $this->tokenStorage->getToken()->getUser();
        }

        return $user;
    }

    /**
     * Notify project team for the virtual machine state update
     *
     * @param VmStateUpdateEvent $event
     */
    public function notifyVmStateChange(VmStateUpdateEvent $event)
    {
        $virtualMachine = $event->getVm();
        $user           = null;
        if (!is_null($this->tokenStorage->getToken())) {
            $user = $this->tokenStorage->getToken()->getUser();
        }

        if ($virtualMachine->getMailingList()) {
            $tpl           = 'instanceStopped.text.twig';
            $subject       = 'Novactive Hosting : Project platform ' . $virtualMachine->getName() . ' stopped';
            $messageHeader = sprintf('vm-%s-stopped', $virtualMachine->getId());

            if ($virtualMachine->isStarted()) {
                $tpl           = 'instanceStarted.text.twig';
                $subject       = 'Novactive Hosting : Project platform ' . $virtualMachine->getName() . ' started';
                $messageHeader = sprintf('vm-%s-started', $virtualMachine->getId());
            }

            $message = $this->mailer->createMessage();
            $message->setSubject($subject)
                    ->setFrom($this->notificationConfig['from'])
                    ->setTo($virtualMachine->getMailingList())
                    ->setCc($this->notificationConfig['cc'])
                    ->setBody(
                        $this->tplEngine->render(
                            sprintf('InfraVirtualMachineBundle:emails:%s', $tpl),
                            array(
                                'instance' => $virtualMachine,
                                'user'     => $user
                            )
                        ),
                        'text/plain'
                    );
            $message->getHeaders()->addTextHeader('X-Message-ID', $messageHeader);
            $this->mailer->send($message);
        }
    }
}
