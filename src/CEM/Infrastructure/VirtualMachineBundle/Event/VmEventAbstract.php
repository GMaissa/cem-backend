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

namespace CEM\Infrastructure\VirtualMachineBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use CEM\Domain\VirtualMachine\Model\VirtualMachineInterface;

/**
 * Virtual machine post load event
 */
abstract class VmEventAbstract extends Event
{
    /**
     * @var VirtualMachineInterface
     */
    protected $virtualMachine;

    public function __construct(VirtualMachineInterface $virtualMachine)
    {
        $this->virtualMachine = $virtualMachine;
    }

    /**
     * Retrieve the virtual machine
     *
     * @return VirtualMachineInterface
     */
    public function getVm(): VirtualMachineInterface
    {
        return $this->virtualMachine;
    }
}
