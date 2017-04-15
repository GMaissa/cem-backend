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

namespace CEM\Infrastructure\UserBundle\Security\Provider;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Serializer\Exception\UnsupportedException;

class UserProvider implements UserProviderInterface
{
    protected $userRepository;

    /**
     * UserProvider constructor.
     *
     * @param EntityRepository $userRepository
     */
    public function __construct(EntityRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function loadUserByUsername($username)
    {
        $user = $this->userRepository->findOneBy(array('username' => $username));
        if (null === $user) {
            $message = sprintf(
                'Unable to find an active User object identified by "%s"',
                $username
            );
            throw new UsernameNotFoundException($message);
        }

        return $user;
    }

    public function refreshUser(UserInterface $user)
    {
        $class = get_class($user);
        if (false == $this->supportsClass($class)) {
            throw new UnsupportedException(
                sprintf(
                    'Instances of "%s" are not supported',
                    $class
                )
            );
        }

        return $this->userRepository->find($user->getId());
    }

    public function supportsClass($class)
    {
        $obj = new \ReflectionClass($class);

        return (
            $obj->getName() == $this->userRepository->getClassName() ||
            $obj->isSubclassOf($this->userRepository->getClassName())
        );
    }
}
