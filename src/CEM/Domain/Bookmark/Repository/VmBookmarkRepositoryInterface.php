<?php
/**
 * File part of the VirtualMachine Dashboard
 *
 * @category  CEM
 * @package   CEM.Domain.Bookmark
 * @author    Guillaume Maïssa <pro.g@maissa.fr>
 * @copyright 2017 Guillaume Maïssa
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace CEM\Domain\Bookmark\Repository;

use CEM\Domain\Bookmark\Exception\VmAlreadyBookmarkedException;
use CEM\Domain\Bookmark\Exception\VmBookmarkNotFoundException;
use CEM\Domain\Bookmark\ValueObject\VmBookmark;
use CEM\Domain\User\Model\UserInterface;
use CEM\Domain\VirtualMachine\Model\VirtualMachineInterface;

/**
 * Interface class for Virtual Machine Bookmarks Repository
 */
interface VmBookmarkRepositoryInterface
{
    /**
     * Retrieve user bookmarks
     *
     * @param UserInterface $user
     *
     * @return array of VmBookmark
     */
    public function findAllByUser(UserInterface $user): array;

    /**
     * Find a bookmark
     *
     * @param UserInterface           $user
     * @param VirtualMachineInterface $virtualMachine
     *
     * @return mixed
     */
    public function findVmBookmarked(UserInterface $user, VirtualMachineInterface $virtualMachine);

    /**
     * Save Virtual machine to user's bookmarks
     *
     * @param VmBookmark $bookmark
     *
     * @return mixed
     * @throws VmAlreadyBookmarkedException when the virtual machine is already bookmarked
     */
    public function save(VmBookmark $bookmark);

    /**
     * Delete virtual machine bookmark
     *
     * @param VmBookmark $bookmark
     *
     * @return mixed
     * @throws VmBookmarkNotFoundException if no bookmark found
     */
    public function delete(VmBookmark $bookmark);

    /**
     * Check if a vm is in the user's bookmarks
     *
     * @param UserInterface           $user
     * @param VirtualMachineInterface $virtualMachine
     *
     * @return mixed
     */
    public function isInUserBookmarks(UserInterface $user, VirtualMachineInterface $virtualMachine);
}
