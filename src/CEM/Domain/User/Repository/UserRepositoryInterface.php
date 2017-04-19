<?php
/**
 * File part of the VirtualMachine Dashboard
 *
 * @category  CEM
 * @package   CEM.Domain.User
 * @author    Guillaume Maïssa <pro.g@maissa.fr>
 * @copyright 2017 Guillaume Maïssa
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
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
