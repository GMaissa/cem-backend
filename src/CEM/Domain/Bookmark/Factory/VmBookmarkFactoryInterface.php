<?php
/**
 * File part of the Cloud Environments Management Backend
 *
 * @category  CEM
 * @package   CEM.Domain.Bookmark
 * @author    Guillaume Maïssa <pro.g@maissa.fr>
 * @copyright 2017 Guillaume Maïssa
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace CEM\Domain\Bookmark\Factory;

use CEM\Domain\Bookmark\ValueObject\VmBookmark;
use CEM\Domain\User\Model\UserInterface;
use CEM\Domain\VirtualMachine\Model\VirtualMachineInterface;

/**
 * Virtual machine bookmark factory interface
 */
interface VmBookmarkFactoryInterface
{
    /**
     * Create new virtual machine bookmark
     *
     * @param UserInterface           $user
     * @param VirtualMachineInterface $virtualMachine
     *
     * @return VmBookmark
     */
    public function build(UserInterface $user, VirtualMachineInterface $virtualMachine): VmBookmark;
}
