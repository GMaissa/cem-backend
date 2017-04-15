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

namespace CEM\Infrastructure\UserBundle\Repository;

use Doctrine\ORM\EntityRepository;
use CEM\Domain\User\Model\UserInterface;
use CEM\Domain\User\Repository\UserRepositoryInterface;

/**
 * Doctrine User Repository Class
 */
class DoctrineUserRepository extends EntityRepository implements UserRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function delete(UserInterface $user)
    {
        $this->_em->remove($user);
        $this->_em->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function save(UserInterface $user)
    {
        $this->_em->persist($user);
        $this->_em->flush();
    }
}
