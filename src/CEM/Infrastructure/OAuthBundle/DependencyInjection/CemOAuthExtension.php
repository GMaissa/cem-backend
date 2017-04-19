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

namespace CEM\Infrastructure\OAuthBundle\DependencyInjection;

use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Yaml\Yaml;

/**
 * Infrastructure OAuthBundle Dependency Injection Class
 */
class CemOAuthExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        // Nothing to load
    }

    /**
     * Loads UserBundle configuration.
     *
     * @param ContainerBuilder $container
     */
    public function prepend(ContainerBuilder $container)
    {
        $persistenceConfigFile = __DIR__ . '/../Resources/config/persistence.yml';
        $config                = Yaml::parse(file_get_contents($persistenceConfigFile));
        $container->prependExtensionConfig('doctrine', $config);
        $container->addResource(new FileResource($persistenceConfigFile));
    }
}
