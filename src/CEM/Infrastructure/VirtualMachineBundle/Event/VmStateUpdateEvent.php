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

namespace CEM\Infrastructure\VirtualMachineBundle\Event;

/**
 * Virtual machine state update event
 */
class VmStateUpdateEvent extends VmEventAbstract
{
    const NAME = 'cem_virtual_machine.vm.state_update';
}
