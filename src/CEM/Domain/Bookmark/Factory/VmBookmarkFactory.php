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

namespace CEM\Domain\Bookmark\Factory;

use CEM\Domain\Bookmark\ValueObject\VmBookmark;
use CEM\Domain\User\Model\UserInterface;
use CEM\Domain\VirtualMachine\Model\VirtualMachineInterface;

/**
 * Virtual machine bookmark factory class
 */
class VmBookmarkFactory implements VmBookmarkFactoryInterface
{
    /**
     * @var string
     */
    private $bookmarkClass;

    /**
     * Set the bookmark class name
     *
     * @param string $class
     */
    public function setBookmarkClass(string $class)
    {
        $this->bookmarkClass = $class;
    }

    /**
     * {@inheritdoc}
     */
    public function build(UserInterface $user, VirtualMachineInterface $virtualMachine): VmBookmark
    {
        return new $this->bookmarkClass($user, $virtualMachine->getId());
    }
}
