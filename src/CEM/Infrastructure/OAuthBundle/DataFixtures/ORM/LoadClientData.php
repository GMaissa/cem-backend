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

namespace CEM\Infrastructure\OAuthBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use CEM\Infrastructure\OAuthBundle\Model\Client;

class LoadClientData implements FixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $oauthClient = new Client();
        $oauthClient->setRandomId('2s9gmnszs82so0c0so440k4o44g0cgs4oscsk884g0ww04ggko');
        $oauthClient->setSecret('5cg9wo8qqsg0sg08gkgwgw8kgscg8wo8ww88c0444ock4gwws');
        $oauthClient->setRedirectUris(['']);
        $oauthClient->setAllowedGrantTypes(['password', 'refresh_token']);

        $manager->persist($oauthClient);
        $manager->flush();
    }
}
