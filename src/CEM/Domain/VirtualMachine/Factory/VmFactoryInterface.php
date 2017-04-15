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

namespace CEM\Domain\VirtualMachine\Factory;

use CEM\Domain\VirtualMachine\Model\VirtualMachineInterface;

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
