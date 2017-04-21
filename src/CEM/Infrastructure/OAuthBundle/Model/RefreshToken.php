<?php
/**
 * File part of the Cloud Environments Management Backend
 *
 * @category  CEM
 * @package   CEM.Infrastructure.OAuthBundle
 * @author    Guillaume Maïssa <pro.g@maissa.fr>
 * @copyright 2017 Guillaume Maïssa
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace CEM\Infrastructure\OAuthBundle\Model;

use FOS\OAuthServerBundle\Entity\RefreshToken as BaseRefreshToken;
use CEM\Infrastructure\UserBundle\Model\User;

/**
 * Refresh Token Model
 */
class RefreshToken extends BaseRefreshToken
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
