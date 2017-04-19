<?php
/**
 * File part of the VirtualMachine Dashboard
 *
 * @category  CEM
 * @package   CEM.Infrastructure.VirtualMachineBundle
 * @author    Guillaume Maïssa <pro.g@maissa.fr>
 * @copyright 2017 Guillaume Maïssa
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
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
