<?php
/**
 * File part of the VirtualMachine Dashboard
 *
 * @category  CEM
 * @package   CEM.Infrastructure.OAuthBundle
 * @author    Guillaume Maïssa <pro.g@maissa.fr>
 * @copyright 2017 Guillaume Maïssa
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace CEM\Infrastructure\OAuthBundle\Model;

use FOS\OAuthServerBundle\Entity\AccessToken as BaseAccessToken;
use CEM\Infrastructure\UserBundle\Model\User;

/**
 * Access Token Model
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
