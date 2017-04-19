<?php
/**
 * File part of the VirtualMachine Dashboard
 *
 * @category  CEM
 * @package   CEM.Infrastructure.BookmarkBundle
 * @author    Guillaume Maïssa <pro.g@maissa.fr>
 * @copyright 2017 Guillaume Maïssa
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace CEM\Infrastructure\BookmarkBundle\Repository;

use Doctrine\ORM\EntityRepository;
use CEM\Domain\Bookmark\Exception\VmAlreadyBookmarkedException;
use CEM\Domain\Bookmark\Exception\VmBookmarkNotFoundException;
use CEM\Domain\Bookmark\ValueObject\VmBookmark;
use CEM\Domain\Bookmark\Repository\VmBookmarkRepositoryInterface;
use CEM\Domain\User\Model\UserInterface;
use CEM\Domain\VirtualMachine\Model\VirtualMachineInterface;

/**
 * Virtual machine bookmarks repository class
 */
class VmBookmarkRepository extends EntityRepository implements VmBookmarkRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findAllByUser(UserInterface $user): array
    {
        $vmBookmarks = $this->findBy(['user' => $user]);

        return $vmBookmarks;
    }

    /**
     * {@inheritdoc}
     */
    public function findVmBookmarked(UserInterface $user, VirtualMachineInterface $virtualMachine)
    {
        $vmBookmark = $this->findOneBy(['user' => $user, 'vmId' => $virtualMachine->getId()]);

        if (!$vmBookmark) {
            throw new VmBookmarkNotFoundException(
                sprintf("No user bookmark found for provided virtual machine ID %s", $virtualMachine->getId())
            );
        }

        return $vmBookmark;
    }

    /**
     * {@inheritdoc}
     */
    public function save(VmBookmark $vmBookmark)
    {
        $existingBookmark = $this->findOneBy(['vmId' => $vmBookmark->getVmId(), 'user' => $vmBookmark->getUser()]);

        if ($existingBookmark) {
            throw new VmAlreadyBookmarkedException(
                sprintf("The virtual machine %s is already bookmarked", $vmBookmark->getVmId())
            );
        }

        $this->_em->persist($vmBookmark);
        $this->_em->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function delete(VmBookmark $vmBookmark)
    {
        $this->_em->remove($vmBookmark);
        $this->_em->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function isInUserBookmarks(UserInterface $user, VirtualMachineInterface $virtualMachine)
    {
        return $this->createQueryBuilder('b')
                    ->select('count(b.vmId)')
                    ->where('b.user = ?1')
                    ->andWhere('b.vmId = ?2')
                    ->setParameters(
                        [1 => $user, 2 => $virtualMachine->getId()]
                    )
                    ->getQuery()
                    ->getSingleScalarResult();
    }
}
