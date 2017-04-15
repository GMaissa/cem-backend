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

namespace CEM\Infrastructure\OAuthBundle\Model;

use FOS\OAuthServerBundle\Entity\AccessToken as BaseAccessToken;
use CEM\Infrastructure\UserBundle\Model\User;

/**
 * Class AccessToken
 */
class AccessToken extends BaseAccessToken
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var User
     */
    protected $user;
}
