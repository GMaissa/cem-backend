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

namespace CEM\Infrastructure\UserBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Yaml\Yaml;

/**
 * Infrastructure UserBundle Dependency Injection Class
 */
class InfraUserExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');
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

        $serializerConfigFile = __DIR__ . '/../Resources/config/serializer.yml';
        $config               = Yaml::parse(file_get_contents($serializerConfigFile));
        $container->prependExtensionConfig('jms_serializer', $config);
        $container->addResource(new FileResource($serializerConfigFile));
    }
}
