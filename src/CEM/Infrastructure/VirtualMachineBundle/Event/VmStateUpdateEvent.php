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

/**
 * Virtual machine state update event
 */
class VmStateUpdateEvent extends VmEventAbstract
{
    const NAME = 'vm_dashboard.vm.state_update';
}
