<?php
/**
 * @author    Philip Bergman <philip@zicht.nl>
 * @copyright Zicht Online <http://www.zicht.nl>
 * @date      10/22/15
 * @time      3:29 PM
 */
namespace PBergman\Bundle\BeanstalkBundle\Tests;

use PBergman\Bundle\BeanstalkBundle\Server\Configuration;
use PBergman\Bundle\BeanstalkBundle\Server\Manager;
use PBergman\Bundle\BeanstalkBundle\Service\Beanstalk;

class BeanstalkTest extends \PHPUnit_Framework_TestCase
{
    public function testBeanstalk()
    {
        $manager = new Manager();
        $beanstalk = new Beanstalk($manager);
        $this->assertNull($beanstalk->getDispatcher());
        $this->assertSame($manager, $beanstalk->getConnectionManager());
        $manager['default1'] = new Configuration('127.0.0.1', 12345);
        $manager['default2'] = new Configuration('127.0.0.1', 67890);
        $manager['default3'] = new Configuration('127.0.0.1', 99999);
        $producer = $beanstalk->getProducer('default2');
        $this->assertEquals($producer, $beanstalk->getProducer('default2'));
        $this->assertNotEquals($producer, $beanstalk->getProducer('default3'));
    }
}