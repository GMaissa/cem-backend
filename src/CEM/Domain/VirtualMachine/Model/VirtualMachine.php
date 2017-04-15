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

namespace CEM\Domain\VirtualMachine\Model;

use CEM\Domain\VirtualMachine\Exception\VmStateUpdateException;

/**
 * Virtual Machine Model Class
 */
class VirtualMachine implements VirtualMachineInterface
{
    /**
     * VirtualMachine identifier
     *
     * @var string
     */
    private $id;

    /**
     * VirtualMachine name
     *
     * @var string
     */
    private $name;

    /**
     * VirtualMachine EC2 type
     *
     * @var string
     */
    private $type;

    /**
     * VirtualMachine state (ex: running, stopped)
     *
     * @var integer
     */
    private $state;

    /**
     * VirtualMachine area zone
     *
     * @var string
     */
    private $az;

    /**
     * VirtualMachine private IP
     *
     * @var string
     */
    private $privateIp;

    /**
     * VirtualMachine public IP
     *
     * @var string
     */
    private $publicIp;

    /**
     * Mailing lists to be notified on instance state update
     *
     * @var array
     */
    private $mailingList = [];

    /**
     * Should the instance be kept alive
     *
     * @var boolean
     */
    private $keepAlive = false;

    /**
     * Should the instance be started automatically every day
     *
     * @var boolean
     */
    private $autoStart = false;

    /**
     * Is instance in current user's bookmarks
     *
     * @var boolean
     */
    private $isInBookmarks = false;

    /**
     * VirtualMachine constructor.
     *
     * @param array $data
     */
    public function __construct($data = array())
    {
        foreach ($data as $field => $value) {
            $this->$field = $value;
        }
    }

    /**
     * Get id
     *
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get public ip
     *
     * @return string
     */
    public function getPublicIp(): string
    {
        return $this->publicIp;
    }

    /**
     * Get mailing list
     *
     * @return array
     */
    public function getMailingList(): array
    {
        return $this->mailingList;
    }

    /**
     * Check if the Virtual Machine is started
     *
     * @return bool
     */
    public function isStarted(): bool
    {
        return $this->state == self::STATE_STARTED;
    }

    /**
     * Check if the Virtual Machine is stopped
     *
     * @return bool
     */
    public function isStopped(): bool
    {
        return $this->state == self::STATE_STOPPED;
    }

    /**
     * Check if the Virtual Machine is either started or stopped
     *
     * @return bool
     */
    public function isStartedOrStopped(): bool
    {
        return ($this->isStarted() || $this->isStopped()) ? true : false;
    }

    /**
     * Check if the Virtual Machine can be started
     *
     * @return bool
     */
    public function canStart(): bool
    {
        return $this->isStopped();
    }

    /**
     * Check if the Virtual Machine can be stopped
     *
     * @return bool
     */
    public function canStop(): bool
    {
        return $this->isStarted();
    }

    /**
     * Start the Virtual Machine
     *
     * @throws VmStateUpdateException when not possible to start Virtual Machine
     */
    public function start()
    {
        if (!$this->canStart()) {
            throw new VmStateUpdateException(
                sprintf(
                    'Impossible to start Virtual Machine %s',
                    $this->getId()
                )
            );
        }

        $this->state = self::STATE_STARTED;
    }

    /**
     * Stop the Virtual Machine
     *
     * @throws VmStateUpdateException when not possible to stop Virtual Machine
     */
    public function stop()
    {
        if (!$this->canStop()) {
            throw new VmStateUpdateException(
                sprintf(
                    'Impossible to stop Virtual Machine %s',
                    $this->getId()
                )
            );
        }

        $this->state = self::STATE_STOPPED;
    }

    /**
     * Set the virtual machine public IP
     *
     * @param string $publicIp
     */
    public function setPublicIp(string $publicIp)
    {
        $this->publicIp = $publicIp;
    }

    /**
     * Set the Virtual Machine as bookmarked
     */
    public function bookmark()
    {
        $this->isInBookmarks = true;
    }
}
