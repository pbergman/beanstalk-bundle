<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\Bundle\BeanstalkBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class BeanstalkExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('service.xml');

        if ($config['debug'] && $container->hasDefinition('beanstalk.debug')) {
            $container->getDefinition('beanstalk.debug')->addTag('kernel.event_subscriber');
        } elseif ($container->hasDefinition('beanstalk.debug')) {
            $container->removeDefinition('beanstalk.debug');
        }

        if ($container->hasDefinition('beanstalk.server.manager')) {
            $managerDefinition = $container->getDefinition('beanstalk.server.manager');
            foreach ($config['servers'] as $name => $config) {
                $serviceName = sprintf('beanstalk.server.connection.%s', $name);
                $connDefinition = (new Definition('PBergman\Bundle\BeanstalkBundle\Server\Configuration', $config))->setPublic(false);
                $container->setDefinition($serviceName, $connDefinition);
                $managerDefinition->addMethodCall('addConfiguration', [$name, new Reference($serviceName)]);

            }
        }
    }
}