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

namespace CEM\Domain\User\Repository;

use CEM\Domain\User\Model\UserInterface;

/**
 * Interface class for User Repository
 */
interface UserRepositoryInterface
{
    /**
     * Retrieve a User matching the criteria
     *
     * @param array      $criteria
     * @param array|null $orderBy
     *
     * @return UserInterface \ null
     */
    public function findOneBy(array $criteria, array $orderBy = null);

    /**
     * Delete a user
     *
     * @param UserInterface $user
     */
    public function delete(UserInterface $user);

    /**
     * Create a user
     *
     * @param UserInterface $user
     */
    public function save(UserInterface $user);
}
