<?php
/**
 * @author    Philip Bergman <philip@zicht.nl>
 * @copyright Zicht Online <http://www.zicht.nl>
 */
namespace PBergman\Bundle\BeanstalkBundle\Tests\Traits;

use PBergman\Bundle\BeanstalkBundle\Server\Configuration;
use PBergman\Bundle\BeanstalkBundle\Server\Connection;
use PBergman\Bundle\BeanstalkBundle\Server\Manager;
use PBergman\Bundle\BeanstalkBundle\Service\Beanstalk;
use PBergman\Bundle\BeanstalkBundle\Tests\Helper\ConfigurationTestHelper;
use PBergman\Bundle\BeanstalkBundle\Tests\Helper\ConnectionTestHelper;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class BeanstalkTrait
 *
 * @package     PBergman\Bundle\BeanstalkBundle\Tests\Traits
 */
trait BeanstalkTrait
{
    /**
     * @param EventDispatcherInterface $dispatcher
     * @return Beanstalk
     */
    protected function getNewBeanstalk(EventDispatcherInterface $dispatcher = null)
    {
        $manager = new Manager();
        $manager['default'] = (new Configuration(null))->setConnection(new ConnectionTestHelper());
        return new Beanstalk($manager, $dispatcher);
    }
}