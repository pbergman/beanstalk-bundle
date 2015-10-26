<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\Bundle\BeanstalkBundle\DependencyInjection;

use Pheanstalk\PheanstalkInterface;
use Symfony\Component\Config\Definition\Builder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{

    /**
     * Generates the configuration tree builder.
     *
     * @return Builder\TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder()
    {
        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.
        return
            $this->newRootNode('beanstalk')
                ->children()
                    ->append($this->getServerNode())
                ->end()
            ->end()
        ;
    }

    /**
     * @param   string    $name
     * @return  Builder\ArrayNodeDefinition|Builder\NodeDefinition
     */
    protected function newRootNode($name)
    {
        return (new Builder\TreeBuilder())->root($name);
    }

    /**
     * build server node
     *
     * @return Builder\NodeDefinition
     */
    protected function getServerNode()
    {
        return
            $this->newRootNode('servers')
                ->defaultValue([
                    'default' => [
                        'host'       => '127.0.0.1',
                        'port'       => PheanstalkInterface::DEFAULT_PORT,
                        'timeout'    => null,
                        'persistent' => false,
                    ]
                ])
                ->useAttributeAsKey('name')
                ->prototype('array')
                    ->children()
                        ->scalarNode('host')
                            ->isRequired()
                            ->cannotBeEmpty()
                        ->end()
                        ->integerNode('port')
                            ->defaultValue(PheanstalkInterface::DEFAULT_PORT)
                        ->end()
                        ->integerNode('timeout')
                            ->defaultNull()
                        ->end()
                        ->booleanNode('persistent')
                            ->defaultFalse()
                        ->end()
                    ->end()
                ->end()
        ;
    }
}