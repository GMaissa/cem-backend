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
interface VirtualMachineInterface
{
    const STATE_STARTED  = 1;
    const STATE_STOPPED  = 2;
    const STATE_STOPPING = 3;
    const STATE_STARTING = 4;

    /**
     * VirtualMachine constructor.
     *
     * @param array $data
     */
    public function __construct($data = array());

    /**
     * Get id
     *
     * @return string
     */
    public function getId(): string;

    /**
     * Get name
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Get public ip
     *
     * @return string
     */
    public function getPublicIp(): string;

    /**
     * Get mailing list
     *
     * @return array
     */
    public function getMailingList(): array;

    /**
     * Check if the Virtual Machine is started
     *
     * @return bool
     */
    public function isStarted(): bool;

    /**
     * Check if the Virtual Machine is stopped
     *
     * @return bool
     */
    public function isStopped(): bool;

    /**
     * Check if the Virtual Machine is either started or stopped
     *
     * @return bool
     */
    public function isStartedOrStopped(): bool;

    /**
     * Check if the Virtual Machine can be started
     *
     * @return bool
     */
    public function canStart(): bool;

    /**
     * Check if the Virtual Machine can be stopped
     *
     * @return bool
     */
    public function canStop(): bool;

    /**
     * Start the Virtual Machine
     *
     * @throws VmStateUpdateException when not possible to start Virtual Machine
     */
    public function start();

    /**
     * Stop the Virtual Machine
     *
     * @throws VmStateUpdateException when not possible to stop Virtual Machine
     */
    public function stop();

    /**
     * Set the virtual machine public IP
     *
     * @param string $publicIp
     */
    public function setPublicIp(string $publicIp);
}
