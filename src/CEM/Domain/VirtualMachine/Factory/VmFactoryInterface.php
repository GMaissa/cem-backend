<?php
/**
 * File part of the Cloud Environments Management Backend
 *
 * @category  CEM
 * @package   CEM.Domain.VirtualMachine
 * @author    Guillaume Maïssa <pro.g@maissa.fr>
 * @copyright 2017 Guillaume Maïssa
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace CEM\Domain\VirtualMachine\Factory;

use CEM\Domain\VirtualMachine\Model\VirtualMachineInterface;

/**
 * Virtual Machine Factory Interface
 */
interface VmFactoryInterface
{
    /**
     * Create new VirtualMachine
     *
     * @param array|null $data
     *
     * @return VirtualMachineInterface
     */
    public function build(array $data = null): VirtualMachineInterface;
}
