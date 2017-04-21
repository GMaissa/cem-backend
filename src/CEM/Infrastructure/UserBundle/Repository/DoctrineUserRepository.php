<?php
/**
 * File part of the Cloud Environments Management Backend
 *
 * @category  CEM
 * @package   CEM.Infrastructure.UserBundle
 * @author    Guillaume Maïssa <pro.g@maissa.fr>
 * @copyright 2017 Guillaume Maïssa
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
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
