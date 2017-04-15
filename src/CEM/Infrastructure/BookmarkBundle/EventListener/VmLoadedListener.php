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

namespace CEM\Infrastructure\BookmarkBundle\EventListener;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use CEM\Domain\Bookmark\Repository\VmBookmarkRepositoryInterface;
use CEM\Infrastructure\UserBundle\Model\User;
use CEM\Infrastructure\VirtualMachineBundle\Event\VmPostLoadEvent;

/**
 * VirtualMachine load listener
 */
class VmLoadedListener
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * Sets the virtual machines provider object
     *
     * @param TokenStorageInterface $securityTokenStorage
     */
    public function setSecurityTokenStorage(TokenStorageInterface $securityTokenStorage)
    {
        $this->tokenStorage = $securityTokenStorage;
    }

    /**
     * Virtual machine bookmark service
     * @var VmBookmarkRepositoryInterface
     */
    private $bookmarkRepository;

    /**
     * sets the user service
     *
     * @param VmBookmarkRepositoryInterface $bookmarkRepository
     */
    public function setBookmarkRepository(VmBookmarkRepositoryInterface $bookmarkRepository)
    {
        $this->bookmarkRepository = $bookmarkRepository;
    }

    /**
     * Set the VirtualMachine as bookmarked if in user bookmarks
     *
     * @param VmPostLoadEvent $event
     */
    public function onVmPostLoad(VmPostLoadEvent $event)
    {
        $user = $this->getCurrentUser();
        if ($user) {
            $virtualMachine = $event->getVm();

            if ($this->bookmarkRepository->isInUserBookmarks($user, $virtualMachine)) {
                $virtualMachine->bookmark();
            }
        }
    }

    /**
     * Get currently logged in user
     *
     * @return null|User
     */
    protected function getCurrentUser()
    {
        $user = null;
        if (!is_null($this->tokenStorage->getToken())) {
            $user = $this->tokenStorage->getToken()->getUser();
        }

        return $user;
    }
}
