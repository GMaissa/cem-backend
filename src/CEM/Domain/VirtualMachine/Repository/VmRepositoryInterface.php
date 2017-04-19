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

namespace CEM\Domain\VirtualMachine\Repository;

use CEM\Domain\VirtualMachine\Exception\VmNotFoundException;
use CEM\Domain\VirtualMachine\Model\VirtualMachineInterface;

/**
 * Interface class for Virtual Machine Repository
 */
interface VmRepositoryInterface
{
    /**
     * Retrieve a Virtual Machine details
     *
     * @param string $vmId
     *
     * @return VirtualMachineInterface
     * @throws VmNotFoundException if not instance is found with provided id
     */
    public function find(string $vmId): VirtualMachineInterface;

    /**
     * Find all instances
     *
     * @return mixed
     */
    public function findAll();

    /**
     * Retrieve list of Virtual Machines matching provided filters
     *
     * Possible filters keys:
     *   - vmTypes: development|production
     *   - vmIds:
     *   - keepAlive: boolean
     *   - autoStart: boolean
     *   - states:
     *
     * @param array $filters
     *
     * @return mixed
     */
    public function findBy($filters = array());

    /**
     * Update Virtual Machine state
     *
     * @param VirtualMachineInterface $virtualMachine
     */
    public function save(VirtualMachineInterface $virtualMachine);
}
