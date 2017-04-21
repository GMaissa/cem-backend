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

namespace CEM\Domain\Bookmark\ValueObject;

use CEM\Domain\User\Model\UserInterface;

/**
 * Virtual Machine Bookmark Model Class
 */
class VmBookmark
{
    /**
     * @var UserInterface
     */
    private $user;

    /**
     * @var string
     */
    private $vmId;

    /**
     * Model constructor
     *
     * @param UserInterface $user
     * @param string        $vmId
     */
    public function __construct(UserInterface $user, string $vmId)
    {
        $this->user = $user;
        $this->vmId = $vmId;
    }

    /**
     * Retrieve the bookmarked virtual machine
     *
     * @return string
     */
    public function getVmId(): string
    {
        return $this->vmId;
    }

    /**
     * Retrieve the bookmark user
     *
     * @return UserInterface
     */
    public function getUser()
    {
        return $this->user;
    }
}
